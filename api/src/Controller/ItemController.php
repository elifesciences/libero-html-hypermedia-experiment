<?php

namespace App\Controller;

use App\Http\JsonLdResponse;
use App\Items;
use OutOfBoundsException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ItemController
{
    private $items;
    private $urlGenerator;

    public function __construct(Items $items, UrlGeneratorInterface $urlGenerator)
    {
        $this->items = $items;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(string $id) : Response
    {
        if (null === $id) {
            throw new NotFoundHttpException();
        }

        try {
            $item = $this->items->get($id);
        } catch (OutOfBoundsException $e) {
            throw new NotFoundHttpException('', $e);
        }

        $json = [
            '@context' => 'http://schema.org/',
            '@type' => array_unique([$item['type'], 'http://schema.org/Article']),
            '@id' => $this->urlGenerator->generate('item', ['id' => $item['id']], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => [
                '@type' => 'rdf:HTML',
                '@value' => $item['title'],
            ],
            'identifier' => [
                [
                    '@type' => 'Identifier',
                    'propertyID' => 'http://libero.pub/id',
                    'value' => $item['id'],
                ],
            ],
        ];

        if (isset($item['doi'])) {
            $json['identifier'][] = [
                '@type' => 'Identifier',
                'propertyID' => 'https://identifiers.org/doi',
                'value' => $item['doi'],
            ];
        }

        if (isset($item['datePublished'])) {
            $json['datePublished'] = [
                '@type' => 'xsd:date',
                '@value' => $item['datePublished'],
            ];
        }

        if (isset($item['description'])) {
            $json['description'] = [
                '@type' => 'rdf:HTML',
                '@value' => $item['description'],
            ];
        }

        if (isset($item['jats'])) {
            $json['encoding'][] = [
                '@type' => 'MediaObject',
                'contentUrl' => $item['jats'],
                'encodingFormat' => 'application/jats+xml',
                'inLanguage' => 'en',
            ];
        }

        if (isset($item['content'])) {
            $json['articleBody'] = [
                '@type' => 'rdf:HTML',
                '@value' => $item['content'],
            ];
        }

        return new JsonLdResponse($json);
    }
}
