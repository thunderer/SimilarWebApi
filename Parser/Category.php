<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\Parser;

class Category extends Parser
    {
    public function processJson(array $response)
        {
        if(!array_key_exists('Category', $response))
            {
            throw $this->createInvalidResponseException('JSON', $response);
            }
        return $response['Category'];
        }

    public function processXml(\SimpleXMLElement $response)
        {
        if(!isset($response->Category[0]))
            {
            throw $this->createInvalidResponseException('XML', $response);
            }
        return $response->Category[0];
        }
    }