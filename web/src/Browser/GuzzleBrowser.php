<?php

namespace App\Browser;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Link;

final class GuzzleBrowser implements Browser
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function request(string $method, string $uri) : PromiseInterface
    {
        return $this->client->requestAsync(
            $method,
            $uri,
            [
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                },
            ]
        )
            ->then(
                function (ResponseInterface $response) use (&$url) : Crawler {
                    return new Crawler((string) $response->getBody(), $url);
                }
            );
    }

    public function click(Link $link) : PromiseInterface
    {
        if ($link instanceof Form) {
            return $this->submit($link);
        }

        return $this->request($link->getMethod(), $link->getUri());
    }

    public function submit(Form $form) : PromiseInterface
    {
        return $this->request($form->getMethod(), $form->getUri());
    }
}
