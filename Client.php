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
            throw new \InvalidArgumentException(sprintf('Invalid response format: %s, allowed: %s!', $format, implode(', ', $allowedFormats)));
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
     * @throws \InvalidArgumentException When given call is not supported by library
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
            throw new \RuntimeException(sprintf('Call %s using format %s failed with code %s!', $call, $this->format, $code));
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
     * @return string Response text
     */
    public static function executeCall($call, $domain, $format, $token)
        {
        $target = 'http://api.similarweb.com/Site/'.$domain.'/'.$call.'?'.http_build_query(array(
            'Format' => $format,
            'UserKey' => $token,
            ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($info, $response);
        }
    }