<?php

namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient extends Client
{
    private $maxRetries;
    private $retryDelay;
    private $timeout;

    public function __construct(array $config = []) {
        parent::__construct($config);

        // Set the default options for the retry logic
        $this->maxRetries = (isset($config["maxRetries"])) ? $config["maxRetries"] : 5;
        $this->retryDelay = (isset($config["retryDelay"])) ? $config["retryDelay"] : 1000;
        $this->timeout = (isset($config["timeout"])) ? $config["timeout"] : 10000;
    }

    /**
     * Retry logic for the specified method with a configurable number of retries.
     *
     * @param string $functionNameToWrap
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    private function wrapper($functionNameToWrap, $url = null, $options = [])
    {
        $options['timeout'] = $this->timeout / 1000;

        $retryCount = 0;
        do {
            try {
                return parent::$functionNameToWrap($url, $options);
            } catch (RequestException $e) {
                if ($retryCount >= $this->maxRetries || !$this->shouldRetry($e)) {
                    throw $e;
                }
                usleep($this->retryDelay * 1000);
            }
            $retryCount++;
        } while ($retryCount < $this->maxRetries);

        throw $e;
    }

    /**
     * Determine if a request should be retried.
     *
     * @param RequestException $exception
     * @return bool
     */
    protected function shouldRetry(RequestException $exception)
    {
        // Retry logic based on the HTTP status code, e.g., retry if 5xx server error
        return $exception->getResponse() !== null && floor($exception->getResponse()->getStatusCode() / 100) === 5;
    }

    /**
     * Retry logic for the get method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function get($url = null, $options = [])
    {
        return $this->wrapper('get', $url, $options);
    }

    /**
     * Retry logic for the head method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function head($url = null, $options = [])
    {
        return $this->wrapper('head', $url, $options);
    }

    /**
     * Retry logic for the delete method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function delete($url = null, $options = [])
    {
        return $this->wrapper('delete', $url, $options);
    }

    /**
     * Retry logic for the put method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function put($url = null, $options = [])
    {
        return $this->wrapper('put', $url, $options);
    }

    /**
     * Retry logic for the patch method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function patch($url = null, $options = [])
    {
        return $this->wrapper('patch', $url, $options);
    }

    /**
     * Retry logic for the post method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function post($url = null, $options = [])
    {
        return $this->wrapper('post', $url, $options);
    }

    /**
     * Retry logic for the options method.
     *
     * @param string|null $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function options($url = null, $options = [])
    {
        return $this->wrapper('options', $url, $options);
    }
}
