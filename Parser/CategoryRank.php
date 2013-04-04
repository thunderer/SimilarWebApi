<?php
namespace Thunder\Api\SimilarWeb\Parser;

use Thunder\Api\SimilarWeb\Parser;

class CategoryRank extends Parser
    {
    public function processJson(array $response)
        {
        $return = array(
            'name' => $response['Category'],
            'rank' => intval($response['CategoryRank']),
            );
        if(!$return['name'] && !$return['rank'])
            {
            return array();
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array(
            'name' => $response->Category[0],
            'rank' => intval($response->CategoryRank[0]),
            );
        if(!$return['name'] && !$return['rank'])
            {
            return array();
            }
        return $return;
        }
    }