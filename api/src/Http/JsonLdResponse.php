<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class JsonLdResponse extends JsonResponse
{
    public function __construct(array $data, int $status = Response::HTTP_OK, array $headers = [])
    {
        $this->encodingOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

        $headers['Content-Type'] = 'application/ld+json';

        parent::__construct($data, $status, $headers);
    }
}
