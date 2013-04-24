<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class SearchIntelligence extends Parser
    {
    public function processJson(array $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response['StartDate']),
            'endDate' => $this->convertMonthYearToTimestamp($response['EndDate']),
            'organicSearchShare' => floatval($response['OrganicSearchShare']),
            'paidSearchShare' => floatval($response['PaidSearchShare']),
            'topOrganicTerms' => array(),
            'topPaidTerms' => array(),
            );
        if(!$response['TopOrganicTerms'] || !$response['TopPaidTerms'])
            {
            return array();
            }
        foreach($response['TopOrganicTerms'] as $item)
            {
            $return['topOrganicTerms'][] = $item;
            }
        foreach($response['TopPaidTerms'] as $item)
            {
            $return['topPaidTerms'][] = $item;
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response->StartDate),
            'endDate' => $this->convertMonthYearToTimestamp($response->EndDate),
            'organicSearchShare' => floatval($response->OrganicSearchShare),
            'paidSearchShare' => floatval($response->PaidSearchShare),
            'topOrganicTerms' => array(),
            'topPaidTerms' => array(),
            );
        if(!isset($response->TopOrganicTerms[0]->Keyword) || !isset($response->TopPaidTerms[0]->Keyword))
            {
            return array();
            }
        $items = count($response->TopOrganicTerms->Keyword);
        for($i = 0; $i < $items; $i++)
            {
            $return['topOrganicTerms'][] = strip_tags($response->TopOrganicTerms->Keyword[$i]->asXml());
            }
        $items = count($response->TopPaidTerms->Keyword);
        for($i = 0; $i < $items; $i++)
            {
            $return['topPaidTerms'][] = strip_tags($response->TopPaidTerms->Keyword[$i]->asXml());
            }
        return $return;
        }
    }