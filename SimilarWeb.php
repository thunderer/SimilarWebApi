<?php
namespace Thunder\Api\SimilarWeb;

/**
 * SimilarWeb API client.
 *
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class SimilarWeb
    {
    protected $userKey;
    protected $format;
    protected $cache = array();

    /* ---------------------------------------------------------------------- */
    /* --- METHODS ---------------------------------------------------------- */
    /* ---------------------------------------------------------------------- */

    /**
     * Initialize API client environment
     *
     * @param string $userKey API User Key
     * @param string $format Response format
     * @throws \InvalidArgumentException When either User Key or format is invalid
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
     * Get API endpoint URL address
     *
     * @param string $call API call name
     * @param string $url Domain name
     * @return string API endpoint URL address
     */
    public function getUrlTarget($call, $url)
        {
        return sprintf('http://api.similarweb.com/Site/%s/%s?Format=%s&UserKey=%s', $url, $call, $this->format, $this->userKey);
        }

    /**
     * Call API endpoint and return nicely formatted data
     *
     * @param string $call API call name
     * @param string $url Domain name
     * @param bool $force Force request / override cache
     * @return string|array Depends on specific API call
     * @throws \RuntimeException When request or parsing response failed
     * @throws \InvalidArgumentException When invalid or unsupported call or format is given
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
     * Wrapper for cURL requests to API endpoints
     *
     * @param string $call API call name
     * @param string $url Target URL
     * @return array Result (integer status code, string result)
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