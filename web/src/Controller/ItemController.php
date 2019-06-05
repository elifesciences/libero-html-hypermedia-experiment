<?php

namespace App\Controller;

use App\Browser\Browser;
use Generator;
use GuzzleHttp\Promise\Coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ItemController
{
    private $browser;
    private $twig;

    public function __construct(Browser $browser, Environment $twig)
    {
        $this->browser = $browser;
        $this->twig = $twig;
    }

    public function __invoke(string $id) : PromiseInterface
    {
        return new Coroutine(
            function () use ($id) : Generator {
                /** @var Crawler $home */
                $home = yield $this->browser->request('GET', '');

                $getItem = $home->filter('#get_item')->form();
                $getItem['id'] = $id;

                /** @var Crawler $item */
                $item = yield $this->browser->submit($getItem);

                $data = [
                    'title' => $item->filter('#title')->html(),
                ];

                $publishedDate = $item->filter('#published-date');
                $description = $item->filter('#description');
                $content = $item->filter('#content');
                $jats = $item->filter('[rel="alternate"][type="application/jats+xml"]');

                if (count($publishedDate)) {
                    $data['published_date'] = $publishedDate->attr('datetime');
                }

                if (count($description)) {
                    $data['description'] = $description->html();
                }

                if (count($content)) {
                    $data['content'] = $content->html();
                } elseif (count($jats)) {
                    /** @var Crawler $jats */
                    $jats = yield $this->browser->click($jats->link());
                    $data['content'] = $jats->filter('article > body')->text();
                }

                yield new Response($this->twig->render('item.html.twig', ['item' => $data]));
            }
        );
    }
}
