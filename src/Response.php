<?php
declare(strict_types=1);

namespace Minitoot;

class Response
{
    private int $code;
    private array $headers;
    private string $body;

    public function __construct(int $code, array $headers, string $body)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function bodyAsJson()
    {
        return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
    }
}