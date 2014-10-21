<?php
namespace Thunder\SimilarWebApi;

use Symfony\Component\Yaml\Yaml;

class Client
    {
    private $token;
    private $format;
    private $cache;
    private $mapping;

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
        if(!in_array($format, $allowedFormats))
            {
            $message = 'Invalid response format: %s, allowed: %s!';
            sprintf($message, $format, implode(', ', $allowedFormats));
            throw new \InvalidArgumentException();
            }

        $this->token = $token;
        $this->format = $format;
        $this->mapping = null;
        $this->clearCache();
        }

    public function clearCache()
        {
        $this->cache = array();
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
        if(isset($this->cache[$call][$domain]))
            {
            return $this->cache[$call][$domain];
            }

        $endpoint = $this->getEndpoint($call);

        list($code, $content) = $this->executeCall($endpoint->getPath(), $domain, $this->format, $this->token);
        if($code < 200 || $code >= 400)
            {
            $message = 'Call %s using format %s failed with code %s (URL: %s)!';
            $url = $this->getCallUrl($call, $domain, $this->format, 'SECRET_TOKEN_IS_SECRET');
            throw new \RuntimeException(sprintf($message, $call, $this->format, $code, $url));
            }
        $response = $endpoint->getResponse($content, $this->format);
        $this->cache[$call][$domain] = $response;

        return $response;
        }

    /**
     * Returns endpoint (API call handler) for given call name
     *
     * @param string $name API call name
     *
     * @return Endpoint
     *
     * @throws \InvalidArgumentException When given endpoint does not exist
     */
    private function getEndpoint($name)
        {
        if(null === $this->mapping)
            {
            $this->mapping = Yaml::parse(file_get_contents(__DIR__.'/../mapping.yaml'));
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
    public function executeCall($call, $domain, $format, $token)
        {
        $target = $this->getCallUrl($call, $domain, $format, $token);
        $ch = curl_init($target);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            ));
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($code, $response);
        }

    /**
     * Generate target call URL
     *
     * @param string $call Call name as in URL path, eg. v1/traffic
     * @param string $domain Checked domain name
     * @param string $format Response format - XML|JSON
     * @param string $token User's API key
     *
     * @return string
     */
    private function getCallUrl($call, $domain, $format, $token)
        {
        $args = http_build_query(array(
            'Format' => $format,
            'UserKey' => $token,
            ));

        return sprintf('http://api.similarweb.com/Site/%s/%s?%s', $domain, $call, $args);
        }
    }
