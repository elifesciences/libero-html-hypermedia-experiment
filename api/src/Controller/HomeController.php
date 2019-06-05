<?php

namespace App\Controller;

use App\Items;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class HomeController
{
    private $items;
    private $twig;

    public function __construct(Environment $twig, Items $items)
    {
        $this->twig = $twig;
        $this->items = $items;
    }

    public function __invoke() : Response
    {
        return new Response($this->twig->render('home.html.twig', ['items' => $this->items]));
    }
}
