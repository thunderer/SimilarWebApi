<?php
namespace Thunder\Api\SimilarWeb;

class SimilarWeb
    {
    protected $userKey;
    protected $defaultResponseFormat;
    protected $resultCache;
    protected $supportedFormats = array('XML', 'JSON');
    protected $countryData = null;
    protected $validCalls = array(
        'GlobalRank',
        'CountryRank',
        'CategoryRank',
        'Tags',
        'SimilarSites',
        'Category',
        );
    
    public function __construct($userKey, $defaultFormat = 'JSON')
        {
        $this->setUserKey($userKey);
        $this->setDefaultResponseFormat($defaultFormat);
        $this->clearResultCache();
        }

    public function setUserKey($userKey)
        {
        if(!$this->isValidUserKey($userKey))
            {
            throw new \RuntimeException(sprintf('Invalid user API key: %s!', $userKey));
            }
        $this->userKey = $userKey;
        }

    public function getUserKey()
        {
        return $this->userKey;
        }

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

    public function setDefaultResponseFormat($format)
        {
        if(!$this->isSupportedFormat($format))
            {
            throw new \RuntimeException(sprintf('Invalid default response format: %s!', $format));
            }
        $this->defaultResponseFormat = $format;
        }

    public function getDefaultResponseFormat()
        {
        return $this->defaultResponseFormat;
        }

    public function clearResultCache()
        {
        $this->resultCache = array();
        }

    public function getUrlTarget($call, $url, $format)
        {
        return 'http://api.similarweb.com/Site/'.$url.'/'.$call.'?Format='.$format.'&UserKey='.$this->userKey;
        }

    public function api($call, $url, $format = null)
        {
        if(null === $format)
            {
            $format = $this->defaultResponseFormat;
            }
        if(!$this->isSupportedFormat($format))
            {
            throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
            }
        if(!in_array($call, $this->validCalls))
            {
            throw new \InvalidArgumentException(sprintf('Invalid call: %s!', $call));
            }
        $result = $this->executeCurlRequest($this->getUrlTarget($call, $url, $format));
        if(200 != $result[0])
            {
            throw new \RuntimeException(sprintf('API request failed with code %s!', $result[0]));
            }
        $method = 'parse'.$call.'Response';
        if(method_exists($this, $method))
            {
            $process = '';
            if('JSON' == $format)
                {
                $process = json_decode($result[1], true);
                if(null === $process)
                    {
                    throw new \RuntimeException(sprintf('Failed to decode JSON response: %s!', $result[1]));
                    }
                }
            else if('XML' == $format)
                {
                $process = simplexml_load_string($result[1]);
                if(false === $process)
                    {
                    throw new \RuntimeException(sprintf('Failed to decode XML response: %s!', $result[1]));
                    }
                }
            return call_user_func_array(array($this, $method), array(
                'result' => $process,
                'format' => $format,
                ));
            }
        else
            {
            throw new \RuntimeException(sprintf(
                'Invalid API call: %s for URL %s with format %s!',
                $call, $url, $format));
            }
        }

    protected function isSupportedFormat($format)
        {
        return in_array($format, $this->supportedFormats);
        }

    protected function isValidUserKey($userKey)
        {
        return (bool)preg_match('/^[a-z0-9]{32}$/', $userKey);
        }

    protected function executeCurlRequest($url)
        {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($responseCode, $result);
        }

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

    protected function parseGlobalRankResponse($result, $format = null)
        {
        if('JSON' == $format)
            {
            return $result['Rank'];
            }
        else if('XML' == $format)
            {
            return intval($result->Rank[0]);
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCountryRankResponse($result, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($result['TopCountryRanks'] as $country)
                {
                $return[$country['Code']] = $country['Rank'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($result->TopCountryRanks[0]->CountryRank))
                {
                return array();
                }
            $items = count($result->TopCountryRanks->CountryRank);
            for($i = 0; $i < $items; $i++)
                {
                $return[intval($result->TopCountryRanks->CountryRank[$i]->Code)] = intval($result->TopCountryRanks->CountryRank[$i]->Rank);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCategoryRankResponse($result, $format = null)
        {
        if('JSON' == $format)
            {
            $return = array(
                'name' => $result['Category'],
                'rank' => intval($result['CategoryRank']),
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
                'name' => $result->Category[0],
                'rank' => intval($result->CategoryRank[0]),
                );
            if(!$return['name'] && !$return['rank'])
                {
                return -1;
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseTagsResponse($result, $format = null)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($result['Tags'] as $country)
                {
                $return[$country['Name']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($result->Tags[0]->Tag))
                {
                return array();
                }
            $items = count($result->Tags->Tag);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($result->Tags->Tag[$i]->Name->asXml())] = floatval($result->Tags->Tag[$i]->Score);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseSimilarSitesResponse($result, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            foreach($result['SimilarSites'] as $country)
                {
                $return[$country['Url']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            if(!isset($result->SimilarSites[0]->SimilarSite))
                {
                return array();
                }
            $items = count($result->SimilarSites->SimilarSite);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($result->SimilarSites->SimilarSite[$i]->Url->asXml())]
                    = floatval($result->SimilarSites->SimilarSite[$i]->Score);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCategoryResponse($result, $format)
        {
        if('JSON' == $format)
            {
            return $result['Category'];
            }
        else if('XML' == $format)
            {
            return $result->Category[0];
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }
    }