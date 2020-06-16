<?php
declare(strict_types=1);

namespace Minitoot;

class Client
{
    public const OOB_CALLBACK_URI = 'urn:ietf:wg:oauth:2.0:oob';

    private string $host;
    private ConnectorInterface $connector;

    public function __construct(string $host, ConnectorInterface $connector)
    {
        $this->host = $host;
        $this->connector = $connector;
    }

    public function registerApplication(string $clientName, string $redirectUris, ?string $scopes = null, ?string $website = null)
    {
        $params = [
            'client_name' => $clientName,
            'redirect_uris' => $redirectUris,
        ];
        if ($scopes !== null) {
            $params['scopes'] = $scopes;
        }
        if ($website !== null) {
            $params['website'] = $website;
        }

        $res = $this->connector->post($this->makeEndpoint('/api/v1/apps'), $params);
        foreach ($res->getHeaders() as $h) {
            [$k, $v] = $h;
            fprintf(STDERR, "<-- {$k}: {$v}\n");
        }
        switch ($res->getCode()) {
            case 200:
            case 422:
                return $res->bodyAsJson();
            default:
                throw new \RuntimeException();
        }
    }

    private function makeEndpoint(string $path): string
    {
        if (strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }
        return "https://{$this->host}/{$path}";
    }
}