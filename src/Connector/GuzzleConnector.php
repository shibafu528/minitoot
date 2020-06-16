<?php
declare(strict_types=1);

namespace Minitoot\Connector;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Minitoot\ConnectionException;
use Minitoot\ConnectorInterface;
use Minitoot\Response;

class GuzzleConnector implements ConnectorInterface
{
    public function post(string $url, array $params = [], array $headers = []): Response
    {
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->post($url, [
                RequestOptions::FORM_PARAMS => $params,
                RequestOptions::HEADERS => $headers,
                RequestOptions::HTTP_ERRORS => false,
            ]);
            return new Response($res->getStatusCode(), $this->normalizeHeaders($res->getHeaders()), $res->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new ConnectionException('Exception in request: ' . $e->getMessage(), 0, $e);
        }
    }

    private function normalizeHeaders(array $responseHeaders)
    {
        $headers = [];
        foreach ($responseHeaders as $key => $values) {
            foreach ($values as $value) {
                $headers[] = [$key, $value];
            }
        }
        return $headers;
    }
}