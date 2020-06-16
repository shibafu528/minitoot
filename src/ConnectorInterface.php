<?php
declare(strict_types=1);

namespace Minitoot;

interface ConnectorInterface
{
    public function post(string $url, array $params = [], array $headers = []): Response;
}