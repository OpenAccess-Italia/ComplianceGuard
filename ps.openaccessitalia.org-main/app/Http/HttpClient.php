<?php

namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class HttpClient extends Client
{
    private $maxRetries;
    private $retryDelay;
    private $timeout;

    public function __construct(array $config = []) {
        parent::__construct($config);

        // Set the default options for the retry logic
        $this->maxRetries = (isset($config["maxRetries"])) ? $config["maxRetries"] : 5;
        $this->retryDelay = (isset($config["retryDelay"])) ? $config["retryDelay"] : 2000;
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
        Log::info("Sending \"$functionNameToWrap\" request to \"$url\"");

        $options['timeout'] = $this->timeout / 1000;

        $retryCount = 1;

        while(true) {

            Log::info("Attempt $retryCount/$this->maxRetries...");
            try {
                $response = parent::$functionNameToWrap($url, $options);
                $statusCode = $response->getStatusCode();
                Log::info("Success. HTTP Status code: $statusCode");
                return $response;
            } catch (RequestException $e) {
                Log::debug("Request failed...");
                
                // Log the response HTTP status
                $response = $e->getResponse();
                if($response){
                    $statusCode = $response->getStatusCode();
                    $reasonPhrase = $response->getReasonPhrase();
                    Log::info("[HTTP $statusCode]: $reasonPhrase");
                }

                // If we should not retry the request again, throw the last error.
                if ($retryCount >= $this->maxRetries || !$this->shouldRetry($e)) {
                    throw $e;
                }

                // Wait retryDelay ms and retry the request.
                Log::info("Retrying in ".($this->retryDelay)." ms...");
                usleep($this->retryDelay * 1000);
            }
            
            $retryCount++;
        }
    }

    /**
     * Determine if a request should be retried.
     *
     * @param RequestException $exception
     * @return bool
     */
    protected function shouldRetry(RequestException $exception)
    {
        $response = $exception->getResponse();
        if($response){
            $statusCode = $response->getStatusCode();
            if(
                $statusCode == 429 || // HTTP Too Many Requests Error
                ($statusCode >= 500 && $statusCode <= 599) // HTTP 5xx Error
            ) {
                return true;
            }

            return false;
        }

        // This is to allow retry on timeouts.
        return true;
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
