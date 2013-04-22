<?php
namespace Thunder\SimilarWebApi;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class Parser
    {
    /**
     * Parse JSON string into array
     *
     * @param string $response JSON string to process
     * @return array Decoded JSON array
     * @throws \InvalidArgumentException When failed to decode JSON string
     */
    public function prepareJson($response)
        {
        $process = json_decode($response, true);
        if(null === $process)
            {
            throw new \InvalidArgumentException(sprintf('Failed to decode %s response: %s!', 'JSON', $response));
            }
        return $process;
        }

    /**
     * Parse XML string into object
     *
     * @param string $response XML string to process
     * @return \SimpleXMLElement Decoded XML object structure
     * @throws \InvalidArgumentException When failed to decode XML string
     */
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

    /**
     * Handle parsing and analyzing JSON response in derived classes
     *
     * @param string $response Response string
     * @return mixed Depends on API call
     */
    public function parseJson($response)
        {
        return $this->processJson($this->prepareJson($response));
        }

    /**
     * Handle parsing and analyzing XML response in derived classes
     *
     * @param string $response Response string
     * @return mixed Depends on API call
     */
    public function parseXml($response)
        {
        return $this->processXml($this->prepareXml($response));
        }

    /**
     * Generic parsing method
     *
     * @param string $response Response data
     * @param string $format Response format
     * @return mixed Depends on API call
     * @throws \InvalidArgumentException When specified format is not supported
     */
    public function parse($response, $format)
        {
        $supportedFormats = array('XML', 'JSON');
        $method = 'parse'.ucfirst(strtolower($format));
        if(!method_exists($this, $method))
            {
            $message = 'Unsupported response format: %s. Accepted formats are: %s.';
            throw new \InvalidArgumentException(sprintf($message, $format, implode(',', $supportedFormats)));
            }
        return call_user_func_array(array($this, $method), array(
            'response' => $response,
            ));
        }

    /**
     * Handle analyzing JSON response
     *
     * @param array $response Decoded response
     * @return mixed Depends on API call
     */
    public abstract function processJson(array $response);

    /**
     * Handle analyzing XML response
     *
     * @param \SimpleXMLElement $response Decoded response
     * @return mixed Depends on API call
     */
    public abstract function processXml(\SimpleXMLElement $response);

    /**
     * Utility method for derived classes to fail when response is invalid
     *
     * @param string $format Current response format
     * @param string $response Current response string
     * @return \RuntimeException When response is invalid
     */
    protected function createInvalidResponseException($format, $response)
        {
        return new \RuntimeException(sprintf('Invalid response: %s!', $format, $response));
        }
    }