<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
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