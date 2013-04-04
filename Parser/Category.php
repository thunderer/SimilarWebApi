<?php
namespace Thunder\Api\SimilarWeb\Parser;

use Thunder\Api\SimilarWeb\Parser;

class Category extends Parser
    {
    public function processJson(array $response)
        {
        return $response['Category'];
        }

    public function processXml(\SimpleXMLElement $response)
        {
        return $response->Category[0];
        }
    }