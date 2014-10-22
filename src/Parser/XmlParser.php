<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\AbstractParser;
use Thunder\SimilarWebApi\RawResponse;

final class XmlParser extends AbstractParser
    {
    protected function parse($content)
        {
        libxml_use_internal_errors(true);
        try
            {
            $xml = new \SimpleXMLElement($content);
            }
        catch(\Exception $e)
            {
            throw new \RuntimeException(sprintf('Failed to parse XML data: "%s(...)"!', substr($content, 0, 100)));
            }

        $values = array();
        if(array_key_exists('values', $this->mapping))
            {
            foreach($this->mapping['values'] as $key => $item)
                {
                $values[$key] = (string)$xml->{$item['xml']['field']};
                }
            }

        $arrays = array();
        if(array_key_exists('arrays', $this->mapping))
            {
            foreach($this->mapping['arrays'] as $key => $item)
                {
                $array = array();
                $parts = explode('.', $item['xml']['field']);
                $items = array();
                if(2 == count($parts))
                    {
                    $items = $xml->{$parts[0]}->{$parts[1]};
                    }
                foreach($items as $element)
                    {
                    $array[] = $element;
                    }
                $arrays[$key] = $array;
                }
            }

        $maps = array();
        if(array_key_exists('maps', $this->mapping))
            {
            foreach($this->mapping['maps'] as $key => $item)
                {
                /* --- XML NS --- */
                if('Mobile_RelatedApps' == $this->name)
                    {
                    /** @var $element \SimpleXMLElement */
                    $element = $xml->{'RelatedApps'};
                    $ns = $xml->getNamespaces(true);
                    $map = array();
                    foreach($element->children($ns['d2p1']) as $element)
                        {
                        $map[(string)$element->{$item['xml']['key']}] = (string)$element->{$item['xml']['value']};
                        }
                    $maps[$key] = $map;
                    continue;
                    }
                /* --- XML NS --- */
                $map = array();
                $parts = explode('.', $item['xml']['field']);
                $items = array();
                if(2 == count($parts))
                    {
                    $items = $xml->{$parts[0]}->{$parts[1]};
                    }
                foreach($items as $element)
                    {
                    $map[(string)$element->{$item['xml']['key']}] = (string)$element->{$item['xml']['value']};
                    }
                $maps[$key] = $map;
                }
            }

        $tuples = array();
        if(array_key_exists('tuples', $this->mapping))
            {
            foreach($this->mapping['tuples'] as $key => $item)
                {
                $parts = explode('.', $item['xml']['field']);
                $items = array();
                if(2 == count($parts))
                    {
                    $items = $xml->{$parts[0]}->{$parts[1]};
                    }
                $tuple = array();
                foreach($items as $element)
                    {
                    $array = array();
                    foreach($item['xml']['items'] as $itemData)
                        {
                        $array[$itemData['value']] = (string)$element->{$itemData['key']};
                        }
                    $tuple[(string)$element->{$item['xml']['index']}] = $array;
                    }
                $tuples[$key] = $tuple;
                }
            }

        return new RawResponse($content, $values, $arrays, $maps, $tuples);
        }
    }
