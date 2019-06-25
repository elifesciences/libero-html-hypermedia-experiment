<?php

namespace App\Controller;

use App\Http\JsonLdResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class HomeController
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke() : Response
    {
        return new JsonLdResponse(
            [
                '@context' => 'http://schema.org/',
                '@type' => 'WebAPI',
                '@id' => $this->router->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'name' => 'Example Libero Hypermedia API',
                'potentialAction' => [
                    [
                        '@type' => 'FindAction',
                        '@id' => $this->router->generate('homepage-list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'target' => $this->router->getRouteCollection()->get('items')->getPath(),
                    ],
                    [
                        '@type' => 'FindAction',
                        '@id' => $this->router->generate('find-article', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => $this->router->getRouteCollection()->get('item')->getPath(),
                        ],
                        'id-input' => [
                            '@type' => 'PropertyValueSpecification',
                            'valueRequired' => true,
                            'valueName' => 'id',
                        ],
                        'result' => [
                            '@type' => 'Article',
                        ],
                    ],
                ],
            ]
        );
    }
}
