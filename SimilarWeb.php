<?php
namespace Thunder\Api\SimilarWeb;

class SimilarWeb
    {
    protected $userKey;
    protected $defaultResponseFormat;
    protected $resultCache;
    protected $supportedFormats = array('XML', 'JSON');
    protected $countryData = null;
    
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

    public function getSupportedFormats()
        {
        return $this->supportedFormats;
        }

    public function clearResultCache()
        {
        $this->resultCache = array();
        }

    public function getApiTargetUrl($call, $url, $format)
        {
        return 'http://api.similarweb.com/Site/'.$url.'/'.$call.'?Format='.$format.'&UserKey='.$this->userKey;
        }

    public function getGlobalRank($url, $format = null)
        {
        $format = $this->computeFormat($format);
        $apiTarget = $this->getApiTargetUrl('GlobalRank', $url, $format);
        $result = $this->executeCurlRequest($apiTarget);
        if(200 != $result[0])
            {
            return -1;
            }
        switch($format)
            {
            case 'JSON':
                {
                $json = json_decode($result[1], true);
                return $json['Rank'];
                }
            case 'XML':
                {
                $data = simplexml_load_string($result[1]);
                return intval($data->Rank[0]);
                }
            }
        }

    public function getCountryRank($url, $format = null)
        {
        $result = $this->executeCurlRequest($this->getApiTargetUrl('CountryRank', $url, $this->computeFormat($format)));
        if(200 != $result[0])
            {
            return -1;
            }
        $return = array();
        switch($format)
            {
            case 'JSON':
                {
                $json = json_decode($result[1], true);
                foreach($json['TopCountryRanks'] as $country)
                    {
                    $return[$country['Code']] = $country['Rank'];
                    }
                return $return;
                }
            case 'XML':
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
            }
        }

    public function getCategoryRank($url, $format = null)
        {
        $result = $this->executeCurlRequest($this->getApiTargetUrl('CategoryRank', $url, $this->computeFormat($format)));
        if(200 != $result[0])
            {
            return -1;
            }
        $return = array();
        switch($format)
            {
            case 'JSON':
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
            case 'XML':
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
            }
        }

    public function getWebsiteTags($url, $format = null)
        {
        }

    public function getSimilarSites($url, $format = null)
        {
        }

    public function getWebsiteCategory($url, $format = null)
        {
        $result = $this->executeCurlRequest($this->getApiTargetUrl('Category', $url, $this->computeFormat($format)));
        if(200 != $result[0])
            {
            return -1;
            }
        switch($format)
            {
            case 'JSON':
                {
                $json = json_decode($result[1], true);
                return $json['Category'];
                }
            case 'XML':
                {
                $data = simplexml_load_string($result[1]);
                return $data->Category[0];
                }
            }
        }

    public function api($call, $url, $format = null)
        {
        switch($call)
            {
            case 'GlobalRank':
                {
                return $this->getGlobalRank($url, $format);
                break;
                }
            case 'CountryRank':
                {
                return $this->getCountryRank($url, $format);
                break;
                }
            case 'CategoryRank':
                {
                return $this->getCategoryRank($url, $format);
                break;
                }
            case 'Tags':
                {
                return $this->getWebsiteTags($url, $format);
                break;
                }
            case 'SimilarSites':
                {
                return $this->getSimilarSites($url, $format);
                break;
                }
            case 'Category':
                {
                return $this->getWebsiteCategory($url, $format);
                break;
                }
            default:
                {
                throw new \RuntimeException(sprintf(
                    'Invalid API call: %s for URL %s with format %s!',
                    $call, $url, $format));
                }
            }
        }

    protected function computeFormat($format)
        {
        if(null === $format)
            {
            return $this->defaultResponseFormat;
            }
        if(!$this->isSupportedFormat($format))
            {
            throw new \RuntimeException(sprintf('Invalid format: %s!', $format));
            }
        return $format;
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
        $result = null; // curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($responseCode, $result);
        }

    protected function loadCountryData($file = 'iso3166.csv', $forceReload = false)
        {
        if(is_array($this->countryData) && !$forceReload)
            {
            return;
            }
        $lines = @file($file);
        $countries = array();
        $regexp = '/^([A-Z]{2})\s([A-Z]{2})\s([A-Z]{3}|null)\s([0-9]{1,3}|null)\s([^\n]+)$/';
        if($lines)
            {
            foreach($lines as $line)
                {
                $preg = preg_match_all($regexp, $line, $matches, PREG_SET_ORDER);
                if(false !== $preg && isset($matches[0]) && 6 == count($matches[0]))
                    {
                    $countries[intval($matches[0][4])] = $matches;
                    }
                }
            }
        $this->countryData = $countries;
        }
    }