<?php

namespace App\Controller;

use App\Http\JsonLdResponse;
use App\Items;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ItemsController
{
    private $items;
    private $urlGenerator;

    public function __construct(Items $items, UrlGeneratorInterface $urlGenerator)
    {
        $this->items = $items;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke() : Response
    {
        $json = [
            '@context' => 'http://schema.org/',
            '@type' => 'http://schema.org/Collection',
            'hasPart' => array_map(
                function (array $item) : string {
                    return $this->urlGenerator->generate(
                        'item',
                        ['id' => $item['id']],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                },
                iterator_to_array($this->items)
            ),
        ];

        return new JsonLdResponse($json);
    }
}
