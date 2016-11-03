<?php
namespace Thunder\SimilarWebApi\Parser;

use Thunder\SimilarWebApi\AbstractParser;
use Thunder\SimilarWebApi\RawResponse;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class JsonParser extends AbstractParser
{
    private function parseValues(array $json)
    {
        $values = array();
        if(array_key_exists('values', $this->mapping)) {
            foreach($this->mapping['values'] as $key => $item) {
                $values[$key] = $json[$item['json']['field']];
            }
        }

        return $values;
    }

    private function parseArrays(array $json)
    {
        $arrays = array();
        if(array_key_exists('arrays', $this->mapping)) {
            foreach($this->mapping['arrays'] as $key => $item) {
                $array = array();
                foreach($json[$item['json']['field']] as $element) {
                    $array[] = $element;
                }
                $arrays[$key] = $array;
            }
        }

        return $arrays;
    }

    private function parseMaps(array $json)
    {
        $maps = array();
        if(array_key_exists('maps', $this->mapping)) {
            foreach($this->mapping['maps'] as $key => $item) {
                $map = array();
                foreach($json[$item['json']['field']] as $element) {
                    $map[$element[$item['json']['key']]] = $element[$item['json']['value']];
                }
                $maps[$key] = $map;
            }
        }

        return $maps;
    }

    private function parseTuples(array $json)
    {
        $tuples = array();
        if(array_key_exists('tuples', $this->mapping)) {
            foreach($this->mapping['tuples'] as $key => $item) {
                $tuple = array();
                foreach($json[$item['json']['field']] as $element) {
                    $array = array();
                    foreach($item['json']['items'] as $itemData) {
                        $array[$itemData['value']] = $element[$itemData['key']];
                    }
                    $tuple[$element[$item['json']['index']]] = $array;
                }
                $tuples[$key] = $tuple;
            }
        }

        return $tuples;
    }

    protected function parse($content)
    {
        $json = json_decode($content, true);
        if(!$json) {
            throw new \RuntimeException(sprintf('Failed to parse JSON data: "%s(...)"!', substr($content, 0, 100)));
        }

        return new RawResponse($content,
            $this->parseValues($json),
            $this->parseArrays($json),
            $this->parseMaps($json),
            $this->parseTuples($json));
    }
}
