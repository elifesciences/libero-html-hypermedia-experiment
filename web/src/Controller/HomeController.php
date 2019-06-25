<?php

namespace App\Controller;

use GuzzleHttp\Psr7\UriResolver;
use ML\JsonLD\DocumentLoaderInterface;
use ML\JsonLD\JsonLD;
use ML\JsonLD\TypedValue;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use function GuzzleHttp\Psr7\uri_for;
use function GuzzleHttp\uri_template;
use function is_iterable;
use function iterator_to_array;
use function preg_match;

final class HomeController
{
    private $loader;
    private $twig;

    public function __construct(DocumentLoaderInterface $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    public function __invoke() : Response
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

        $findAction = $graph->getNode('http://nginx:8081/homepage-list');

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

        $list = $graph->getNodesByType('http://schema.org/Collection')[0];

        $items = array_map(
            function (TypedValue $value) use ($document) : array {
                $document = JsonLD::getDocument(
                    $base = (string) UriResolver::resolve(uri_for($document->getIri()), uri_for($value->getValue())),
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

                foreach ($this->array($article->getProperty('http://schema.org/identifier')) as $identifier) {
                    if (
                        'http://libero.pub/id' !== $identifier->getProperty('http://schema.org/propertyID')->getValue()
                    ) {
                        continue;
                    }

                    $item['id'] = $identifier->getProperty('http://schema.org/value')->getValue();
                }

                return $item;
            },
            $this->array($list->getProperty('http://schema.org/hasPart'))
        );

        return new Response($this->twig->render('home.html.twig', ['items' => $items]));
    }

    private function array($item) : array
    {
        if (null === $item) {
            return [];
        }

        if (is_array($item)) {
            return $item;
        }

        if (is_iterable($item)) {
            return iterator_to_array($item);
        }

        return [$item];
    }
}
