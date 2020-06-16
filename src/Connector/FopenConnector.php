<?php
declare(strict_types=1);

namespace Minitoot\Connector;

use Minitoot\ConnectionException;
use Minitoot\ConnectorInterface;
use Minitoot\Response;

class FopenConnector implements ConnectorInterface
{
    public function post(string $url, array $params = [], array $headers = []): Response
    {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'header'  => array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers),
            'content' => http_build_query($params),
        ]]);

        // fopenなんか使うわけねーだろ、手抜きだ手抜き
        $body = file_get_contents($url, false, $context);
        if ($body === false) {
            throw new ConnectionException();
        }

        [$resCode, $resHeaders] = $this->parseResponseHeader($http_response_header);
        return new Response($resCode, $resHeaders, $body);
    }

    private function parseResponseHeader(array $responseHeaders): array
    {
        $code = null;
        $headers = [];

        foreach ($responseHeaders as $header) {
            $exploded = explode(':', $header, 2);
            if (isset($exploded[1])) {
                $headers[] = array_map('trim', $exploded);
            } elseif (preg_match('#HTTP/[0-9.]+\s+(\d+)#', $header, $matches)) {
                $code = (int)$matches[1];
            }
        }

        return [$code, $headers];
    }
}