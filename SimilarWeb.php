<?php
namespace Thunder\Api\SimilarWeb;

/**
 * SimilarWeb API client.
 *
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class SimilarWeb
    {
    protected $userKey;
    protected $format;
    protected $resultCache;
    protected $supportedFormats = array('XML', 'JSON');
    protected $countryData = null;
    protected $userKeyRegexp = '/^[a-z0-9]{32}$/';
    protected $messages = array(
        'invalid_user_key' => 'Invalid or empty user API key: %s. Key must be 32 lowercase alphanumeric characters.',
        'invalid_format' => 'Unsupported response format: %s. Accepted formats are: %s.',
        'request_failed' => 'API request failed with code %s, response: "%s".',
        'invalid_call' => 'Invalid API call: %s for URL %s with format %s!',
        'invalid_response' => 'Failed to decode %s response: %s!',
        );
    protected $apiTarget = 'http://api.similarweb.com/Site/%s/%s?Format=%s&UserKey=%s';
    protected $validCalls = array(
        'GlobalRank',
        'CountryRank',
        'CategoryRank',
        'Tags',
        'SimilarSites',
        'Category',
        );

    /**
     * Initialize API client environment
     *
     * @param string $userKey API User Key
     * @param string $format Response format
     * @throws \InvalidArgumentException When either User Key or default format is invalid
     */
    public function __construct($userKey, $format = 'JSON')
        {
        if(!preg_match($this->userKeyRegexp, $userKey))
            {
            throw new \InvalidArgumentException(sprintf($this->messages['invalid_user_key'], $userKey, $this->userKeyRegexp));
            }
        $this->userKey = $userKey;

        if(!in_array(strtoupper($format), $this->supportedFormats))
            {
            throw new \InvalidArgumentException(sprintf($this->messages['invalid_format'], $format, implode(',', $this->supportedFormats)));
            }
        $this->format = $format;

        $this->resultCache = array();
        }

    /**
     * Helper method for getting country data (API requests return only country
     * code) using integer ISO3166 numeric code. Returns array with all
     * countries data when no country code specified
     *
     * @param int|null $country
     * @return null
     */
    public function getCountryData($country = null)
        {
        $this->loadCountryData();
        if(null === $country)
            {
            return $this->countryData;
            }

        return array_key_exists($country, $this->countryData)
            ? $this->countryData[$country]
            : null;
        }

    /**
     * Get API endpoint URL address
     *
     * @param string $call API call name
     * @param string $url Domain name
     * @param string $format Response format
     * @return string API endpoint URL address
     */
    public function getUrlTarget($call, $url, $format)
        {
        return sprintf($this->apiTarget, $url, $call, $format, $this->userKey);
        }

    /**
     * Call API endpoint and return nicely formatted data
     *
     * @param string $call API call name
     * @param string $url Domain name
     * @param bool $force Do not load result from cache
     * @return string|array Depends on specific API call
     * @throws \RuntimeException When request or parsing response failed
     * @throws \InvalidArgumentException When invalid or unsupported call or format is given
     */
    public function api($call, $url, $force = false)
        {
        if(!in_array($call, $this->validCalls))
            {
            throw new \InvalidArgumentException(sprintf('Invalid call: %s!', $call));
            }
        $result = $this->executeCurlRequest($this->getUrlTarget($call, $url, $this->format));
        if(200 != $result[0])
            {
            throw new \RuntimeException(sprintf($this->messages['request_failed'], $result[0], $result[1]));
            }
        $method = 'parse'.$call.'Response';
        if(!method_exists($this, $method))
            {
            throw new \RuntimeException(sprintf($this->messages['invalid_call'], $call, $url, $this->format));
            }
        $process = '';
        if('JSON' == $this->format)
            {
            $process = json_decode($result[1], true);
            }
        else if('XML' == $this->format)
            {
            libxml_use_internal_errors(true);
            $process = simplexml_load_string($result[1]);
            }
        if(!$process)
            {
            throw new \RuntimeException(sprintf($this->messages['invalid_response'], $this->format, $result[1]));
            }
        return call_user_func_array(array($this, $method), array(
            'result' => $process,
            'format' => $this->format,
            ));
        }

    /**
     * Wrapper for cURL requests to API endpoints
     *
     * @param string $url Target URL
     * @return array Result (integer status code, string result)
     */
    protected function executeCurlRequest($url)
        {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($responseCode, $result);
        }

    /**
     * Load countries data from file provided by library or your own
     *
     * @param string $file Path to country data file
     * @param bool $forceReload Force reload contents if already loaded
     */
    public function loadCountryData($file = null, $forceReload = false)
        {
        if(is_array($this->countryData) && count($this->countryData) && !$forceReload)
            {
            return;
            }
        if(null === $file)
            {
            $file = __DIR__.'/iso3166.csv';
            }
        $lines = file($file);
        $countries = array();
        $regexp = '/^([A-Z]{2})\s([A-Z]{2})\s([A-Z]{3}|null)\s([0-9]{1,3}|null)\s([^\n]+)$/';
        if($lines)
            {
            foreach($lines as $line)
                {
                $preg = preg_match_all($regexp, $line, $matches, PREG_SET_ORDER);
                if(false !== $preg && isset($matches[0]) && 6 == count($matches[0]))
                    {
                    $countries[intval($matches[0][4])] = array(
                        'continent' => $matches[0][1],
                        'twoLetter' => $matches[0][2],
                        'threeLetter' => $matches[0][3],
                        'numeric' => $matches[0][4],
                        'name' => $matches[0][5],
                        );
                    }
                }
            }
        $this->countryData = $countries;
        }

    /* ---------------------------------------------------------------------- */
    /* --- PARSING RESPONSES ------------------------------------------------ */
    /* ---------------------------------------------------------------------- */

    /**
     * Parse GlobalRank response returning rank position number
     *
     * @param array|\SimpleXMLElement $response Response data
     * @param string $format Response format
     * @return int Domain GlobalRank value
     * @throws \InvalidArgumentException When format is not supported
     */
    protected function parseGlobalRankResponse($response, $format)
        {
        if('JSON' == $format)
            {
            return intval($response['Rank']);
            }
        else if('XML' == $format)
            {
            return intval($response->Rank[0]);
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    /**
     * Parse CountryRank response returning list of positions for countries
     *
     * @param array|\SimpleXMLElement $response Response data
     * @param string $format Response format
     * @return array List of CountryRank positions
     * @throws \InvalidArgumentException When format is not supported
     */
    protected function parseCountryRankResponse($response, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($response['TopCountryRanks'] as $country)
                {
                $return[$country['Code']] = $country['Rank'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($response->TopCountryRanks[0]->CountryRank))
                {
                return array();
                }
            $items = count($response->TopCountryRanks->CountryRank);
            for($i = 0; $i < $items; $i++)
                {
                $return[intval($response->TopCountryRanks->CountryRank[$i]->Code)] = intval($response->TopCountryRanks->CountryRank[$i]->Rank);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    /**
     * Parse CategoryRank response returning rank number and category
     *
     * @param array|\SimpleXMLElement $response Response data
     * @param string $format Response format
     * @return array Category name and rank
     * @throws \InvalidArgumentException When format is not supported
     */
    protected function parseCategoryRankResponse($response, $format)
        {
        if('JSON' == $format)
            {
            $return = array(
                'name' => $response['Category'],
                'rank' => intval($response['CategoryRank']),
                );
            if(!$return['name'] && !$return['rank'])
                {
                return -1;
                }
            return $return;
            }
        else if('XML' == $format)
            {
            $return = array(
                'name' => $response->Category[0],
                'rank' => intval($response->CategoryRank[0]),
                );
            if(!$return['name'] && !$return['rank'])
                {
                return -1;
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseTagsResponse($response, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($response['Tags'] as $country)
                {
                $return[$country['Name']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($response->Tags[0]->Tag))
                {
                return array();
                }
            $items = count($response->Tags->Tag);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($response->Tags->Tag[$i]->Name->asXml())] = floatval($response->Tags->Tag[$i]->Score);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseSimilarSitesResponse($response, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($response['SimilarSites'] as $country)
                {
                $return[$country['Url']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($response->SimilarSites[0]->SimilarSite))
                {
                return array();
                }
            $items = count($response->SimilarSites->SimilarSite);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($response->SimilarSites->SimilarSite[$i]->Url->asXml())]
                    = floatval($response->SimilarSites->SimilarSite[$i]->Score);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCategoryResponse($response, $format)
        {
        if('JSON' == $format)
            {
            return $response['Category'];
            }
        else if('XML' == $format)
            {
            return $response->Category[0];
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }
    }