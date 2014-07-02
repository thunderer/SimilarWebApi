<?php
namespace Thunder\SimilarWebApi;

class Endpoint
    {
    private $name;
    private $mapping;

    public function __construct($name, array $mapping)
        {
        $this->name = $name;
        $this->mapping = $mapping;
        }

    public function getResponse($content, $format)
        {
        $response = $this->getInternalResponse($content, $format);
        $class = __NAMESPACE__.'\\Response\\'.$this->name;

        if(!class_exists($class, true))
            {
            throw new \RuntimeException(sprintf('Failed to load response class %s!', $class));
            }

        return new $class($response);
        }

    private function getInternalResponse($content, $format)
        {
        $format = strtoupper($format);
        if('JSON' == $format)
            {
            return $this->parseJson($content);
            }
        else if('XML' == $format)
            {
            return $this->parseXml($content);
            }
        throw new \RuntimeException(sprintf('No parser for call %s using format %s!', $this->name, $format));
        }

    public function getPath()
        {
        if(!array_key_exists('path', $this->mapping))
            {
            throw new \RuntimeException(sprintf('No path was defined for endpoint %s!', $this->name));
            }

        return $this->mapping['path'];
        }

    private function parseJson($content)
        {
        $json = $this->getJsonData($content);

        $values = array();
        if(array_key_exists('values', $this->mapping))
            {
            foreach($this->mapping['values'] as $key => $item)
                {
                $values[$key] = $json[$item['json']['field']];
                }
            }

        $arrays = array();
        if(array_key_exists('arrays', $this->mapping))
            {
            foreach($this->mapping['arrays'] as $key => $item)
                {
                $array = array();
                foreach($json[$item['json']['field']] as $element)
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
                $map = array();
                foreach($json[$item['json']['field']] as $element)
                    {
                    $map[$element[$item['json']['key']]] = $element[$item['json']['value']];
                    }
                $maps[$key] = $map;
                }
            }

        return new RawResponse($content, $values, $arrays, $maps);
        }

    private function parseXml($content)
        {
        $xml = $this->getXmlData($content);

        $values = array();
        if(array_key_exists('values', $this->mapping))
            {
            foreach($this->mapping['values'] as $key => $item)
                {
                $values[$key] = (string)$xml->{$item['json']['field']};
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
                $map = array();
                $parts = explode('.', $item['xml']['field']);
                $items = array();
                if(2 == count($parts))
                    {
                    $items = $xml->{$parts[0]}->{$parts[1]};
                    }
                foreach($items as $element)
                    {
                    $map[(string)$element->{$item['json']['key']}] = (string)$element->{$item['json']['value']};
                    }
                $maps[$key] = $map;
                }
            }

        return new RawResponse($content, $values, $arrays, $maps);
        }

    private function getJsonData($content)
        {
        $json = json_decode($content, true);
        if(!$json)
            {
            throw new \RuntimeException(sprintf('Failed to parse JSON data: "%s(...)"!', substr($content, 0, 100)));
            }

        return $json;
        }

    private function getXmlData($content)
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

        return $xml;
        }
    }