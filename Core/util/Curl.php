<?php

namespace Core\Util;

class Curl
{
    public static function query(string $url, array $data, string $token = null, string $httpMethod = "GET"): bool|string
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($httpMethod === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $headers = [
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json'
        ];

        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
