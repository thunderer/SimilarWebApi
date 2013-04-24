<?php
namespace Thunder\SimilarWebApi\Parser\V2;

use Thunder\SimilarWebApi\Parser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class Traffic extends Parser
    {
    public function processJson(array $response)
        {
        if(!array_key_exists('Date', $response))
            {
            return array();
            }
        $return = array(
            'date' => $this->convertMonthYearToTimestamp($response['Date']),
            'globalRank' => intval($response['GlobalRank']),
            'countryCode' => intval($response['CountryCode']),
            'countryRank' => intval($response['CountryRank']),
            'topCountryShares' => array(),
            'trafficReach' => array(),
            'trafficShares' => array(),
            );
        foreach($response['TopCountryShares'] as $item)
            {
            $return['topCountryShares'][intval($item['CountryCode'])] = floatval($item['TrafficShare']);
            }
        foreach($response['TrafficReach'] as $item)
            {
            $ts = $this->convertDayMonthYearToTimestamp($item['Date']);
            $return['trafficReach'][$ts] = floatval($item['Value']);
            }
        foreach($response['TrafficShares'] as $item)
            {
            $return['trafficShares'][$item['SourceType']] = floatval($item['SourceValue']);
            }
        return $return;
        }

    public function processXml(\SimpleXMLElement $response)
        {
        $return = array(
            'date' => $this->convertMonthYearToTimestamp($response->Date),
            'globalRank' => intval($response->GlobalRank),
            'countryCode' => intval($response->CountryCode),
            'countryRank' => intval($response->CountryRank),
            'topCountryShares' => array(),
            'trafficReach' => array(),
            'trafficShares' => array(),
            );
        if(!isset($response->TopCountryShares[0]->Country)
            || !isset($response->TrafficReach[0]->TrafficReachPoint)
            || !isset($response->TrafficShares[0]->TrafficSource))
            {
            return array();
            }
        $items = count($response->TopCountryShares->Country);
        for($i = 0; $i < $items; $i++)
            {
            $return['topCountryShares'][intval($response->TopCountryShares->Country[$i]->CountryCode)]
                = floatval($response->TopCountryShares->Country[$i]->TrafficShare);
            }
        $items = count($response->TrafficReach->TrafficReachPoint);
        for($i = 0; $i < $items; $i++)
            {
            $ts = $this->convertDayMonthYearToTimestamp($response->TrafficReach->TrafficReachPoint[$i]->Date);
            $return['trafficReach'][$ts] = floatval($response->TrafficReach->TrafficReachPoint[$i]->Value);
            }
        $items = count($response->TrafficShares->TrafficSource);
        for($i = 0; $i < $items; $i++)
            {
            $return['trafficShares'][strip_tags($response->TrafficShares->TrafficSource[$i]->SourceType->asXml())]
                = floatval($response->TrafficShares->TrafficSource[$i]->SourceValue);
            }
        return $return;
        }
    }