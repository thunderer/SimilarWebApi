<?php
namespace Thunder\SimilarWebApi;

/**
 * SimilarWeb API client.
 *
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class Client
    {
    /**
     * Current API UserKey
     *
     * @var string
     */
    protected $userKey;

    /**
     * Desired request / response format
     *
     * @var string
     */
    protected $format;

    /**
     * Cache storage for valid responses
     *
     * @var array
     */
    protected $cache;

    /**
     * Configure client instance
     *
     * @param string $userKey API UserKey
     * @param string $format Desired request / response format
     * @throws \InvalidArgumentException When either is invalid
     */
    public function __construct($userKey, $format = 'JSON')
        {
        if(!preg_match('/^[a-z0-9]{32}$/', $userKey))
            {
            $message = 'Invalid or empty user API key: %s. Key must be 32 lowercase alphanumeric characters.';
            throw new \InvalidArgumentException(sprintf($message, $userKey));
            }
        $this->userKey = $userKey;

        $supportedFormats = array('XML', 'JSON');
        if(!in_array(strtoupper($format), $supportedFormats))
            {
            $message = 'Unsupported response format: %s. Accepted formats are: %s.';
            throw new \InvalidArgumentException(sprintf($message, $format, implode(',', $supportedFormats)));
            }
        $this->format = $format;

        $this->cache = array();
        }

    /**
     * Compute API call target URL
     *
     * @param $call
     * @param $url
     * @return string
     */
    public function getUrlTarget($call, $url)
        {
        return sprintf('http://api.similarweb.com/Site/%s/%s?Format=%s&UserKey=%s', $url, $call, $this->format, $this->userKey);
        }

    /**
     * Call API and analyze it or fetch previous result from cache
     *
     * @param string $call API call name
     * @param string $url URL (domain) to process
     * @param bool $force Force request / override cache
     * @return mixed Depends on API call
     * @throws \RuntimeException When request failed
     */
    public function api($call, $url, $force = false)
        {
        if(isset($this->cache[$call][$url]) && !$force)
            {
            return $this->cache[$call][$url];
            }

        list($status, $response) = $this->executeRequest($call, $url);
        if(200 != $status)
            {
            throw new \RuntimeException(sprintf('API request failed with code %s, response: "%s".', $status, $response));
            }

        $class = __NAMESPACE__.'\\Parser\\'.$call;
        $result = call_user_func_array(array(new $class, 'parse'), array(
            'response' => $response,
            'format' => $this->format,
            ));
        $this->cache[$call][$url] = $result;

        return $result;
        }

    /**
     * Wrapper for cURL request handling
     *
     * @param string $call API call name
     * @param string $url Target URL
     * @return array HTTP status code and response string
     */
    protected function executeRequest($call, $url)
        {
        $urlTarget = $this->getUrlTarget($call, $url);

        $ch = curl_init($urlTarget);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return array($status, $result);
        }
    }