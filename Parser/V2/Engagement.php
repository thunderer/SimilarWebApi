<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class Engagement extends Parser
    {
    public function processJson(array $response)
        {
        $averagePageViews = floatval($response['AveragePageViews']);
        $averageTimeOnSite = floatval($response['AverageTimeOnSite']);
        $bounceRate = floatval($response['BounceRate']);
        if(!$averagePageViews && !$averageTimeOnSite && !$bounceRate)
            {
            return array();
            }
        return array(
            'averagePageViews' => $averagePageViews,
            'averageTimeOnSite' => $averageTimeOnSite,
            'bounceRate' => $bounceRate,
            'date' => $this->convertMonthYearToTimestamp($response['Date']),
            );
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $averagePageViews = floatval($response->AveragePageViews);
        $averageTimeOnSite = floatval($response->AverageTimeOnSite);
        $bounceRate = floatval($response->BounceRate);
        if(!$averagePageViews && !$averageTimeOnSite && !$bounceRate)
            {
            return array();
            }
        return array(
            'averagePageViews' => $averagePageViews,
            'averageTimeOnSite' => $averageTimeOnSite,
            'bounceRate' => $bounceRate,
            'date' => $this->convertMonthYearToTimestamp($response->Date),
            );
        }
    }