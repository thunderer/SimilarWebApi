<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class LeadingDestination extends Parser
    {
    public function processJson(array $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response['StartDate']),
            'endDate' => $this->convertMonthYearToTimestamp($response['EndDate']),
            'sites' => array(),
            );
        if(isset($response['Sites']) && !$response['Sites'])
            {
            return array();
            }
        foreach($response['Sites'] as $item)
            {
            $return['sites'][] = $item;
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response->StartDate),
            'endDate' => $this->convertMonthYearToTimestamp($response->EndDate),
            'sites' => array(),
            );
        if(!isset($response->Sites[0]->Site))
            {
            return array();
            }
        $items = count($response->Sites->Site);
        for($i = 0; $i < $items; $i++)
            {
            $return['sites'][] = strip_tags($response->Sites->Site[$i]->asXml());
            }
        return $return;
        }
    }