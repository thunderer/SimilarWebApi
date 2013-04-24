<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class SocialReferring extends Parser
    {
    public function processJson(array $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response['StartDate']),
            'endDate' => $this->convertMonthYearToTimestamp($response['EndDate']),
            'socialSources' => array(),
            );
        if(!$response['SocialSources'])
            {
            return array();
            }
        foreach($response['SocialSources'] as $item)
            {
            $return['socialSources'][$item['Source']] = floatval($item['Value']);
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array(
            'startDate' => $this->convertMonthYearToTimestamp($response->StartDate),
            'endDate' => $this->convertMonthYearToTimestamp($response->EndDate),
            'socialSources' => array(),
            );
        if(!isset($response->SocialSources[0]->Source))
            {
            return array();
            }
        $items = count($response->SocialSources->Source);
        for($i = 0; $i < $items; $i++)
            {
            $return['socialSources'][strip_tags($response->SocialSources->Source[$i]->Source->asXml())]
                = floatval($response->SocialSources->Source[$i]->Value);
            }
        return $return;
        }
    }