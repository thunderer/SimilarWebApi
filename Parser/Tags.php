<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\Parser;

class Tags extends Parser
    {
    public function processJson(array $response)
        {
        $return = array();
        foreach($response['Tags'] as $country)
            {
            $return[$country['Name']] = $country['Score'];
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array();
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
    }