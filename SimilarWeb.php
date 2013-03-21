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
            return -1;
            }
        $method = 'parse'.$call.'Response';
        if(method_exists($this, $method))
            {
            return call_user_func_array(array($this, $method), array(
                'result' => $result,
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
            $json = json_decode($result[1], true);
            return $json['Rank'];
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            return intval($data->Rank[0]);
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCountryRankResponse($result, $format)
        {
        $return = array();
        if('JSON' == $format)
            {
            $json = json_decode($result[1], true);
            foreach($json['TopCountryRanks'] as $country)
                {
                $return[$country['Code']] = $country['Rank'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            if(!isset($data->TopCountryRanks[0]->CountryRank))
                {
                return array();
                }
            $items = count($data->TopCountryRanks->CountryRank);
            for($i = 0; $i < $items; $i++)
                {
                $return[intval($data->TopCountryRanks->CountryRank[$i]->Code)] = intval($data->TopCountryRanks->CountryRank[$i]->Rank);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCategoryRankResponse($result, $format = null)
        {
        if('JSON' == $format)
            {
            $json = json_decode($result[1], true);
            $return = array(
                'name' => $json['Category'],
                'rank' => intval($json['CategoryRank']),
            );
            if(!$return['name'] && !$return['rank'])
                {
                return -1;
                }
            return $return;
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            $return = array(
                'name' => $data->Category[0],
                'rank' => intval($data->CategoryRank[0]),
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
            $json = json_decode($result[1], true);
            foreach($json['Tags'] as $country)
                {
                $return[$country['Name']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            if(!isset($data->Tags[0]->Tag))
                {
                return array();
                }
            $items = count($data->Tags->Tag);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($data->Tags->Tag[$i]->Name->asXml())] = floatval($data->Tags->Tag[$i]->Score);
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
            $json = json_decode($result[1], true);
            foreach($json['SimilarSites'] as $country)
                {
                $return[$country['Url']] = $country['Score'];
                }
            return $return;
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            if(!isset($data->SimilarSites[0]->SimilarSite))
                {
                return array();
                }
            $items = count($data->SimilarSites->SimilarSite);
            for($i = 0; $i < $items; $i++)
                {
                $return[strip_tags($data->SimilarSites->SimilarSite[$i]->Url->asXml())]
                    = floatval($data->SimilarSites->SimilarSite[$i]->Score);
                }
            return $return;
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }

    protected function parseCategoryResponse($result, $format)
        {
        if('JSON' == $format)
            {
            $json = json_decode($result[1], true);
            return $json['Category'];
            }
        else if('XML' == $format)
            {
            $data = simplexml_load_string($result[1]);
            return $data->Category[0];
            }
        throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
        }
    }