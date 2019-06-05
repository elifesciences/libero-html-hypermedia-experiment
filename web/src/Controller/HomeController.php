<?php

namespace App\Controller;

use App\Browser\Browser;
use Generator;
use GuzzleHttp\Promise\Coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use function GuzzleHttp\Promise\all;

final class HomeController
{
    private $browser;
    private $twig;

    public function __construct(Browser $browser, Environment $twig)
    {
        $this->browser = $browser;
        $this->twig = $twig;
    }

    public function __invoke() : PromiseInterface
    {
        return new Coroutine(
            function () : Generator {
                /** @var Crawler $list */
                $list = yield $this->browser->request('GET', '');

                /** @var array<array<string, mixed>> $items */
                $items = yield all(
                    $list->filter('#items .item')->each(
                        function (Crawler $element) : PromiseInterface {
                            return $this->browser->click($element->link())
                                ->then(
                                    function (Crawler $item) : array {
                                        return [
                                            'id' => $item->filter('#id')->text(),
                                            'title' => $item->filter('#title')->html(),
                                        ];
                                    }
                                );
                        }
                    )
                );

                yield new Response($this->twig->render('home.html.twig', ['items' => $items]));
            }
        );
    }
}
