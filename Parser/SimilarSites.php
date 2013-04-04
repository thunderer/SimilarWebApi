<?php
namespace Thunder\Api\SimilarWeb\Parser;

use Thunder\Api\SimilarWeb\Parser;

class SimilarSites extends Parser
    {
    public function processJson(array $response)
        {
        $return = array();
        foreach($response['SimilarSites'] as $country)
            {
            $return[$country['Url']] = $country['Score'];
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array();
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
    }