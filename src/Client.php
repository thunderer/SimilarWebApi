<?php
namespace Thunder\SimilarWebApi;

use Thunder\SimilarWebApi\Parser\JsonParser;
use Thunder\SimilarWebApi\Parser\XmlParser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class Client
{
    private $token;
    private $format;
    private $cache;

    /**
     * Yep, it's a constructor.
     *
     * @param string $token User key (API token)
     * @param string $format Raw response text format (JSON or XML)
     *
     * @throws \InvalidArgumentException When unsupported format is given
     */
    public function __construct($token, $format)
    {
        $format = strtoupper($format);
        $allowedFormats = array('XML', 'JSON');
        if(!in_array($format, $allowedFormats)) {
            $message = 'Invalid response format: %s, allowed: %s!';
            throw new \InvalidArgumentException(sprintf($message, $format, implode(', ', $allowedFormats)));
        }

        $this->token = $token;
        $this->format = $format;
        $this->clearCache();
    }

    public function clearCache()
    {
        $this->cache = array();
    }

    /**
     * Execute given API call on specified domain
     *
     * @param AbstractRequest $request Call name as in URL path, eg. v1/traffic
     *
     * @return AbstractResponse Value object with interface to fetch results
     */
    public function getResponse(AbstractRequest $request)
    {
        $url = $request->getCallUrl($this->format, $this->token);
        if(isset($this->cache['url'][$url])) {
            return $this->cache['url'][$url];
        }

        $parser = $this->getParser($request);
        $content = $this->executeCall($url);
        $response = $parser->getResponse($content);
        $this->cache['url'][$url] = $response;

        return $response;
    }

    /**
     * Returns endpoint (API call handler) for given call name
     *
     * @param AbstractRequest $request Request object
     *
     * @return ParserInterface
     *
     * @throws \InvalidArgumentException When given endpoint does not exist
     */
    private function getParser(AbstractRequest $request)
    {
        if('JSON' == $this->format) {
            return new JsonParser($request->getName(), $request->getMapping());
        } else if('XML' == $this->format) {
            return new XmlParser($request->getName(), $request->getMapping());
        }

        throw new \RuntimeException(sprintf('Failed to find parser for format %s!', $this->format));
    }

    /**
     * Utility function to execute API call and get raw response text
     *
     * @codeCoverageIgnore
     *
     * @param string $url Call name as in URL path, eg. v1/traffic
     *
     * @return string Response text and status code
     *
     * @throws \RuntimeException If request failed (code outside 2xx range)
     */
    public function executeCall($url)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
        ));
        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($code < 200 || $code >= 400) {
            $message = '%s request %s failed with code %s!';
            $url = str_replace($this->token, 'SECRET_TOKEN_IS_SECRET', $url);
            throw new \RuntimeException(sprintf($message, $this->format, $url, $code), $code);
        }

        return $response;
    }
}
