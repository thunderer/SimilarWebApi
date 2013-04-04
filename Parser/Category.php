<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\Parser;

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