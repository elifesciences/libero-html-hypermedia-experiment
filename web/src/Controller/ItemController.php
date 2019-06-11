<?php

namespace App\Controller;

use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\Literal\Date;
use EasyRdf\Resource;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use function array_reduce;
use function GuzzleHttp\Promise\all;

final class ItemController
{
    private $client;
    private $twig;

    public function __construct(ClientInterface $client, Environment $twig)
    {
        $this->client = $client;
        $this->twig = $twig;
    }

    public function __invoke(?string $id, ?string $doi) : PromiseInterface
    {
        return new Coroutine(
            function () use ($doi, $id) {
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

                $handler = null;
                foreach ($page->allOfType('schema:FindAction') as $find) {
                    /** @var Resource $find */
                    /** @var Resource|null $findHandler */
                    /** @var Resource|null $resultType */
                    $findHandler = $find->get('schema:actionHandler');
                    $resultType = $findHandler->get('schema:result');

                    if (!$resultType->isA('schema:Article')) {
                        continue;
                    }

                    $handler = $findHandler;
                    break;
                }

                if (!$handler instanceof Resource) {
                    throw new RuntimeException('No find form');
                }

                $methodProperty = $handler->get('schema:method');
                $method = $methodProperty ? $methodProperty->getValue() : 'GET';

                $url = $handler->get('schema:url')->getValue();

                $query = array_reduce(
                    $handler->all('schema:requiredProperty'),
                    function (array $query, Resource $value) use ($doi, $id) : array {
                        switch ($name = $value->get('schema:name')->getValue()) {
                            case 'id':
                                $query[$name] = $id ?? $doi;

                                return $query;
                            case 'type':
                                $query[$name] = $doi ? 'https://identifiers.org/doi' : 'http://libero.pub/id';

                                return $query;
                        }

                        throw new RuntimeException("Don't know how to fill in property '{$name}'");
                    },
                    []
                );

                /** @var ResponseInterface $response */
                $response = yield $this->client->requestAsync(
                    $method,
                    $url,
                    [
                        'query' => $query,
                        'on_stats' => function (TransferStats $stats) use (&$url) {
                            $url = $stats->getEffectiveUri();
                        },
                    ]
                );

                $items = new Graph((string) $url, (string) $response->getBody(), 'rdfa');

                /** @var Resource $article */
                $article = $items->allOfType('schema:Article')[0];

                $data = [
                    'title' => $article->getLiteral('schema:name')->getValue(),
                ];

                $data = array_reduce(
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

                $datePublished = $article->getLiteral('schema:datePublished');

                if ($datePublished instanceof Date) {
                    $data['published_date'] = $datePublished->getValue();
                }

                $description = $article->getLiteral('schema:description');

                if ($description instanceof Literal) {
                    $data['description'] = $description->getValue();
                }

                $data = array_reduce(
                    $article->all('schema:encoding'),
                    function (array $data, Resource $encoding) : array {
                        $format = $encoding->getLiteral('schema:encodingFormat')->getValue();
                        $uri = $encoding->get('schema:contentUrl')->getUri();

                        switch ($format) {
                            case 'application/jats+xml':
                                $data['content'] = $this->client->requestAsync('GET', $uri)
                                    ->then(
                                        function (ResponseInterface $response) : string {
                                            return (string) $response->getBody();
                                        }
                                    );
                                break;
                        }

                        return $data;
                    },
                    $data
                );

                yield new Response($this->twig->render('item.html.twig', ['item' => all($data)->wait()]));
            }
        );
    }
}
