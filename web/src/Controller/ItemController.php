<?php

namespace App\Controller;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\UriResolver;
use ML\JsonLD\DocumentLoaderInterface;
use ML\JsonLD\JsonLD;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use function GuzzleHttp\Psr7\uri_for;
use function GuzzleHttp\uri_template;

final class ItemController
{
    private $client;
    private $loader;
    private $twig;

    public function __construct(ClientInterface $client, DocumentLoaderInterface $loader, Environment $twig)
    {
        $this->client = $client;
        $this->loader = $loader;
        $this->twig = $twig;
    }

    public function __invoke(string $id) : Response
    {
        $document = JsonLD::getDocument(
            $_ENV['API_URI'],
            [
                'base' => $_ENV['API_URI'],
                'compactArrays' => false,
                'documentLoader' => $this->loader,
            ]
        );
        $graph = $document->getGraph();

        $findAction = $graph->getNode('http://nginx:8081/find-article');

        $target = $findAction->getProperty('http://schema.org/target');

        $urlTemplate = $target->getProperty('http://schema.org/urlTemplate');

        $inputs = [];
        foreach ($findAction->getProperties() as $name => $value) {
            if (!preg_match('~^http://schema\.org/.+?-input$~', $name)) {
                continue;
            }

            $name = $value->getProperty('http://schema.org/valueName')->getValue();
            $required = 'true' === $value->getProperty('http://schema.org/valueRequired')->getValue();

            switch ($name) {
                case 'id':
                    $inputs[$name] = $id;
                    break;
                default:
                    if ($required) {
                        throw new RuntimeException("Don't know how to set {$name}");
                    }
            }
        }

        $document = JsonLD::getDocument(
            $base = (string) UriResolver::resolve(
                uri_for($document->getIri()),
                uri_for(uri_template($urlTemplate->getValue(), $inputs))
            ),
            [
                'base' => $base,
                'compactArrays' => false,
                'documentLoader' => $this->loader,
            ]
        );
        $graph = $document->getGraph();

        $article = $graph->getNodesByType('http://schema.org/Article')[0];

        $item = [
            'title' => $article->getProperty('http://schema.org/name')->getValue(),
        ];

        if ($description = $article->getProperty('http://schema.org/description')) {
            $item['description'] = $description->getValue();
        }

        if ($datePublished = $article->getProperty('http://schema.org/datePublished')) {
            $item['published_date'] = $datePublished->getValue();
        }

        foreach ($this->iterable($article->getProperty('http://schema.org/identifier')) as $identifier) {
            if (
                'https://identifiers.org/doi' !== $identifier->getProperty('http://schema.org/propertyID')->getValue()
            ) {
                continue;
            }

            $item['doi'] = $identifier->getProperty('http://schema.org/value')->getValue();
        }

        if ($articleBody = $article->getProperty('http://schema.org/articleBody')) {
            $item['content'] = $articleBody->getValue();
        } else {
            foreach ($this->iterable($article->getProperty('http://schema.org/encoding')) as $encoding) {
                $format = $encoding->getProperty('http://schema.org/encodingFormat')->getValue();
                $uri = $encoding->getProperty('http://schema.org/contentUrl')->getId();

                switch ($format) {
                    case 'application/jats+xml':
                        $item['content'] = (string) $this->client->request('GET', $uri)->getBody();
                        break 2;
                }
            }
        }

        return new Response($this->twig->render('item.html.twig', ['item' => $item]));
    }

    private function iterable($item) : iterable
    {
        if (null === $item) {
            return [];
        }

        if (is_iterable($item)) {
            return $item;
        }

        return [$item];
    }
}
