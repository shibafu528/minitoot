<?php
declare(strict_types=1);

namespace Minitoot\Connector;

use Minitoot\ConnectionException;
use Minitoot\ConnectorInterface;
use Minitoot\Response;

class CurlConnector implements ConnectorInterface
{
    public function post(string $url, array $params = [], array $headers = []): Response
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        try {
            $response = curl_exec($ch);
            if ($response == false) {
                $errno = curl_errno($ch);
                $error = curl_error($ch);
                throw new ConnectionException("[cURL Error] {$errno}: {$error}", $errno);
            }

            $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            return new Response($code, $this->parseResponseHeader($header), $body);
        } finally {
            curl_close($ch);
        }
    }

    private function parseResponseHeader(string $headerChunk)
    {
        $headers = [];
        foreach (explode("\n", $headerChunk) as $header) {
            $header = trim($header);
            $exploded = explode(':', $header, 2);
            if (isset($exploded[1])) {
                $headers[] = array_map('trim', $exploded);
            }
        }
        return $headers;
    }
}