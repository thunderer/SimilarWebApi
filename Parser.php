<?php
namespace Thunder\SimilarWebApi;

abstract class Parser
    {
    public function prepareJson($response)
        {
        $process = json_decode($response, true);
        if(null === $process)
            {
            throw new \InvalidArgumentException(sprintf('Failed to decode %s response: %s!', 'JSON', $response));
            }
        return $process;
        }

    public function prepareXml($response)
        {
        libxml_use_internal_errors(true);
        $process = simplexml_load_string($response);
        if(false === $process)
            {
            throw new \InvalidArgumentException(sprintf('Failed to decode %s response: %s!', 'XML', $response));
            }
        return $process;
        }

    public function parseJson($response)
        {
        return $this->processJson($this->prepareJson($response));
        }

    public function parseXml($response)
        {
        return $this->processXml($this->prepareXml($response));
        }

    public function parse($response, $format)
        {
        $supportedFormats = array('XML', 'JSON');
        $method = 'parse'.ucfirst(strtolower($format));
        if(!method_exists($this, $method))
            {
            throw new \InvalidArgumentException(sprintf('Unsupported response format: %s. Accepted formats are: %s.', $format, implode(',', $supportedFormats)));
            }
        return call_user_func_array(array($this, $method), array(
            'response' => $response,
            ));
        }

    public abstract function processJson(array $response);
    public abstract function processXml(\SimpleXMLElement $response);

    protected function createInvalidResponseException($format, $response)
        {
        return new \RuntimeException(sprintf('Invalid response: %s!', $format, $response));
        }
    }