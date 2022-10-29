<?php

namespace App\Helpers;

class RequestHelper
{
    /**
     * Returns GET parameter
     *
     * @param string $paramName
     *
     * @return mixed
     */
    public static function getParam(string $paramName): mixed
    {
        return $_GET[$paramName] ?? null;
    }

    /**
     * Returns POST parameter
     *
     * @param string $paramName
     *
     * @return mixed
     */
    public static function postParam(string $paramName): mixed
    {
        return $_POST[$paramName] ?? null;
    }

    /**
     * Returns GET or POST parameter
     *
     * @param string $paramName
     *
     * @return mixed
     */
    public static function queryParam(string $paramName): mixed
    {
        return self::getParam($paramName) ?: self::postParam($paramName);
    }

    /**
     * Echoes response in JSON format
     *
     * @param array $data
     *
     * @return string
     */
    public static function responseJSON(array $data): string
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Sends CURL request
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @param bool $decodeResult
     *
     * @return array
     */
    public static function sendCurl(string $url, array $data = [], string $method = 'get', array $headers = [], array $options = [], bool $decodeResult = true): array
    {
        $curlOptions = [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ];

        switch ($method) {
            case 'get':
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
                break;
            case 'post':
                $curlOptions[CURLOPT_POST] = true;

                if (!empty($data)) {
                    $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
                }
                break;
        }

        $curlOptions[CURLOPT_URL] = $url;
        $curlOptions += $options;

        $ch = curl_init();

        curl_setopt_array($ch, $curlOptions);

        $result = curl_exec($ch);

        if ($decodeResult) {
            $result = json_decode($result, true);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [
            'result' => $result,
            'httpCode' => $httpCode,
        ];
    }
}
