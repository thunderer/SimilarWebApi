<?php
namespace Thunder\SimilarWebApi;

use Symfony\Component\Yaml\Yaml;

class Client
    {
    private $token;
    private $format;
    private $cache;
    private $mapping;

    public function __construct($token, $format)
        {
        $format = strtoupper($format);
        $allowedFormats = array('XML', 'JSON');
        if(!in_array($format, $allowedFormats))
            {
            $message = 'Invalid response format: %s, allowed: %s!';
            sprintf($message, $format, implode(', ', $allowedFormats));
            throw new \InvalidArgumentException();
            }

        $this->token = $token;
        $this->format = $format;
        $this->cache = array();
        $this->mapping = null;
        }

    /**
     * Execute given API call on specified domain
     *
     * @param string $call Call name as in URL path, eg. v1/traffic
     * @param string $domain Checked domain name
     *
     * @return Response Value object with interface to fetch results
     *
     * @throws \RuntimeException When call failed
     * @throws \LogicException When no response parser was found
     * @throws \InvalidArgumentException When given call is not supported
     */
    public function getResponse($call, $domain)
        {
        /**
         * @var $endpoint Endpoint
         */
        $endpoint = $this->getEndpoint($call);

        list($code, $content) = static::executeCall($endpoint->getPath(), $domain, $this->format, $this->token);
        if($code < 200 || $code >= 400)
            {
            $message = 'Call %s using format %s failed with code %s!';
            throw new \RuntimeException(sprintf($message, $call, $this->format, $code));
            }
        $response = $endpoint->getResponse($content, $this->format);

        return $response;
        }

    protected function getEndpoint($name)
        {
        if(null === $this->mapping)
            {
            $this->mapping = Yaml::parse(file_get_contents(__DIR__.'/mapping.yaml'));
            }
        if(false == array_key_exists($name, $this->mapping))
            {
            throw new \InvalidArgumentException(sprintf('Endpoint %s does not exist!', $name));
            }
        $endpoint = new Endpoint($name, $this->mapping[$name]);

        return $endpoint;
        }

    /**
     * Utility function to execute API call and get raw response text
     *
     * @codeCoverageIgnore
     *
     * @param string $call Call name as in URL path, eg. v1/traffic
     * @param string $domain Checked domain name
     * @param string $format Response format - XML|JSON
     * @param string $token User's API key
     *
     * @return string Response text and status code
     */
    public static function executeCall($call, $domain, $format, $token)
        {
        $args = http_build_query(array(
            'Format' => $format,
            'UserKey' => $token,
            ));
        $target = sprintf('http://api.similarweb.com/Site/%s/%s?%s', $domain, $call, $args);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($code, $response);
        }
    }