<?php
namespace Thunder\SimilarWebApi;

/**
 * Value object for request call parameters.
 */
final class Request
    {
    private $domain;
    private $isMainDomainOnly;
    private $start;
    private $end;
    private $granularity;

    private function __construct()
        {
        }

    /**
     * Standard request - domain only
     *
     * @param $domain
     *
     * @return Request
     */
    public static function Basic($domain)
        {
        $request = new Request();
        $request->setDomain($domain);

        return $request;
        }

    /**
     * @param $domain
     * @param $start
     * @param $end
     * @param $granularity
     * @param $mainDomainOnly
     *
     * @return Request
     */
    public static function Pro($domain, $start, $end, $granularity, $mainDomainOnly)
        {
        $request = new Request();
        $request->setDomain($domain);
        $request->setStartMonth($start);
        $request->setEndMonth($end);
        $request->setGranularity($granularity);
        $request->setMainDomainOnly($mainDomainOnly);

        return $request;
        }

    private function setDomain($domain)
        {
        if(!$domain)
            {
            throw new \InvalidArgumentException('Domain can\'t be empty!');
            }
        $this->domain = $domain;
        }

    private function setStartMonth($start)
        {
        $timestamp = strtotime($start);
        if(!$timestamp)
            {
            throw new \InvalidArgumentException('Invalid start month date!');
            }
        $this->start = date('n-Y', $timestamp);
        }

    private function setEndMonth($end)
        {
        $timestamp = strtotime($end);
        if(!$timestamp)
            {
            throw new \InvalidArgumentException('Invalid end month date!');
            }
        $this->end = date('n-Y', $timestamp);
        }

    private function setGranularity($value)
        {
        if(!in_array($value, array('daily', 'weekly', 'monthly')))
            {
            throw new \InvalidArgumentException('Invalid granularity!');
            }
        $this->granularity = $value;
        }

    private function setMainDomainOnly($value)
        {
        $this->isMainDomainOnly = (bool)$value;
        }

    public function getCallUrl()
        {

        }

    public function getDomain()
        {
        return $this->domain;
        }

    public function getStartMonth()
        {
        return $this->start;
        }

    public function getEndMonth()
        {
        return $this->end;
        }

    public function getGranularity()
        {
        return $this->granularity;
        }

    public function isMainDomainOnly()
        {
        return $this->isMainDomainOnly;
        }
    }
