<?php
namespace Thunder\Api\SimilarWeb\Parser;

use Thunder\Api\SimilarWeb\Parser;

class GlobalRank extends Parser
    {
    public function processJson(array $response)
        {
        if(!array_key_exists('Rank', $response))
            {
            throw $this->createInvalidResponseException('JSON', $response);
            }
        return intval($response['Rank']);
        }

    public function processXml(\SimpleXMLElement $response)
        {
        if(!isset($response->Rank[0]))
            {
            throw $this->createInvalidResponseException('XML', $response);
            }
        return intval($response->Rank[0]);
        }
    }