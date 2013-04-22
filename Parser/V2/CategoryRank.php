<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
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