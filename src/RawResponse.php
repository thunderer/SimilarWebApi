<?php
namespace Thunder\SimilarWebApi;

final class RawResponse
    {
    private $raw;
    private $values;
    private $arrays;
    private $maps;

    public function __construct($raw, array $values, array $arrays, array $maps)
        {
        $this->raw = $raw;
        $this->values = $values;
        $this->arrays = $arrays;
        $this->maps = $maps;
        }

    public function getRaw()
        {
        return $this->raw;
        }

    public function getValues()
        {
        return $this->values;
        }

    public function getArrays()
        {
        return $this->arrays;
        }

    public function getMaps()
        {
        return $this->maps;
        }

    public function hasValue($name)
        {
        return array_key_exists($name, $this->values);
        }

    public function getValue($name)
        {
        if(false == $this->hasValue($name))
            {
            $mapsKeys = $this->maps ? implode(', ', array_keys($this->values)) : 'empty array';
            throw new \RuntimeException(sprintf('Value %s not found among %s!', $name, $mapsKeys));
            }

        return $this->values[$name];
        }

    public function hasArray($name)
        {
        return array_key_exists($name, $this->arrays);
        }

    public function getArray($name)
        {
        if(false == $this->hasArray($name))
            {
            $mapsKeys = $this->maps ? implode(', ', array_keys($this->arrays)) : 'empty array';
            throw new \RuntimeException(sprintf('Array %s not found among %s!', $name, $mapsKeys));
            }

        return $this->arrays[$name];
        }

    public function hasMap($name)
        {
        return array_key_exists($name, $this->maps);
        }

    public function getMap($name)
        {
        if(false == $this->hasMap($name))
            {
            $mapsKeys = $this->maps ? implode(', ', array_keys($this->maps)) : 'empty array';
            throw new \RuntimeException(sprintf('Map %s not found among %s!', $name, $mapsKeys));
            }

        return $this->maps[$name];
        }

    public function getMapValue($name, $key)
        {
        return $this->maps[$name][$key];
        }

    public function getMapKey($name, $value)
        {
        $flip = array_flip($this->maps[$name]);

        return $flip[$value];
        }
    }