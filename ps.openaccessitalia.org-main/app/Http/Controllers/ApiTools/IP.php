<?php

namespace App\Http\Controllers\ApiTools;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class IP extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function query($ip)
    {
        $url = "http://ip-api.com/json/$ip?fields=19970";
        $client = new Client;
        try {
            $response = $client->get($url);
        } catch (BadResponseException $e) {
            return null;
        }
        if (($response->getStatusCode() == 200) && $response->getBody()) {
            $data = json_decode($response->getBody());
            if ($data->status == 'success') {
                return $data;
            }
        }

        return null;
    }

    public static function cidr_match($ip, $range)
    {
        [$subnet, $bits] = explode('/', $range);
        if ($bits === null) {
            $bits = 32;
        }
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }
}
