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

    public function clearResultCache()
        {
        $this->resultCache = array();
        }

    protected function getGlobalRank($result, $format = null)
        {
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
        }

    protected function getCountryRank($result, $format)
        {
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
        }

    protected function getCategoryRank($result, $format = null)
        {
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
        }

    protected function getTags($result, $format = null)
        {
        $return = array();
        switch($format)
            {
            case 'JSON':
                {
                $json = json_decode($result[1], true);
                foreach($json['Tags'] as $country)
                    {
                    $return[$country['Name']] = $country['Score'];
                    }
                return $return;
                }
            case 'XML':
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
        }

    protected function getSimilarSites($result, $format = null)
        {
        $return = array();
        switch($format)
            {
            case 'JSON':
                {
                $json = json_decode($result[1], true);
                foreach($json['SimilarSites'] as $country)
                    {
                    $return[$country['Url']] = $country['Score'];
                    }
                return $return;
                }
            case 'XML':
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
        }

    protected function getCategory($result, $format = null)
        {
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
            default:
                {
                throw new \InvalidArgumentException(sprintf('Invalid format: %s!', $format));
                }
            }
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
        $validCalls = array(
            'GlobalRank',
            'CountryRank',
            'CategoryRank',
            'Tags',
            'SimilarSites',
            'Category',
            );
        if(!in_array($call, $validCalls))
            {
            throw new \InvalidArgumentException(sprintf('Invalid call: %s!', $call));
            }
        $result = $this->executeCurlRequest($this->getUrlTarget($call, $url, $format));
        if(200 != $result[0])
            {
            return -1;
            }
        $method = 'get'.$call;
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