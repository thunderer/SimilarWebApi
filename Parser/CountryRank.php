<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class CountryRank extends Parser
    {
    public function processJson(array $response)
        {
        $return = array();
        if(!array_key_exists('TopCountryRanks', $response))
            {
            throw $this->createInvalidResponseException('JSON', $response);
            }
        foreach($response['TopCountryRanks'] as $country)
            {
            $return[$country['Code']] = $country['Rank'];
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array();
        if(0 === count($response->xpath('/CountryRankResponse/TopCountryRanks')))
            {
            throw $this->createInvalidResponseException('XML', $response->asXML());
            }
        $items = count($response->TopCountryRanks->CountryRank);
        for($i = 0; $i < $items; $i++)
            {
            $return[intval($response->TopCountryRanks->CountryRank[$i]->Code)] = intval($response->TopCountryRanks->CountryRank[$i]->Rank);
            }
        return $return;
        }
    }