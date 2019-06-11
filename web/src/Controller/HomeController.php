<?php

namespace App\Controller;

use EasyRdf\Graph;
use EasyRdf\Resource;
use Generator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use function array_reduce;
use function GuzzleHttp\Promise\all;

final class HomeController
{
    private $client;
    private $twig;

    public function __construct(ClientInterface $client, Environment $twig)
    {
        $this->client = $client;
        $this->twig = $twig;
    }

    public function __invoke() : PromiseInterface
    {
        return new Coroutine(
            function () : Generator {
                /** @var ResponseInterface $response */
                $response = yield $this->client->requestAsync(
                    'GET',
                    '',
                    [
                        'on_stats' => function (TransferStats $stats) use (&$url) {
                            $url = $stats->getEffectiveUri();
                        },
                    ]
                );

                $page = new Graph((string) $url, (string) $response->getBody(), 'rdfa');

                /** @var Resource $list */
                $list = $page->allOfType('schema:Collection')[0];

                /** @var array<array<string, mixed>> $items */
                $items = yield all(
                    array_map(
                        function (Resource $item) : PromiseInterface {
                            return $this->client->requestAsync(
                                'GET',
                                $item->getUri(),
                                [
                                    'on_stats' => function (TransferStats $stats) use (&$url) {
                                        $url = $stats->getEffectiveUri();
                                    },
                                ]
                            )
                                ->then(
                                    function (ResponseInterface $response) use (&$url) : array {
                                        $items = new Graph((string) $url, (string) $response->getBody(), 'rdfa');

                                        /** @var Resource $article */
                                        $article = $items->allOfType('schema:Article')[0];

                                        $data = [
                                            'title' => $article->getLiteral('schema:name')->getValue(),
                                        ];

                                        return array_reduce(
                                            $article->all('schema:identifier'),
                                            function (array $data, Resource $identifier) : array {
                                                switch ($identifier->get('schema:propertyID')->getUri()) {
                                                    case 'http://libero.pub/id':
                                                        $data['id'] = (string) $identifier->getLiteral('schema:value');
                                                        break;
                                                    case 'https://identifiers.org/doi':
                                                        $data['doi'] = (string) $identifier->getLiteral('schema:value');
                                                        break;
                                                }

                                                return $data;
                                            },
                                            $data
                                        );
                                    }
                                );
                        },
                        $list->all('schema:hasPart')
                    )
                );

                yield new Response($this->twig->render('home.html.twig', ['items' => $items]));
            }
        );
    }
}
