<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class AlsoVisited extends Parser
    {
    public function processJson(array $response)
        {
        $ret = array();
        foreach($response['AlsoVisited'] as $item)
            {
            $ret[$item['Url']] = floatval($item['Score']);
            }
        return $ret;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array();
        if(!isset($response->AlsoVisited[0]->AlsoVisited))
            {
            return array();
            }
        $items = count($response->AlsoVisited->AlsoVisited);
        for($i = 0; $i < $items; $i++)
            {
            $return[strip_tags($response->AlsoVisited->AlsoVisited[$i]->Url->asXml())]
                = floatval($response->AlsoVisited->AlsoVisited[$i]->Score);
            }
        return $return;
        }
    }