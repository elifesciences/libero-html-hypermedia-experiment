<?php

namespace App\Controller;

use App\Items;
use OutOfBoundsException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ItemController
{
    private $items;
    private $twig;

    public function __construct(Environment $twig, Items $items)
    {
        $this->twig = $twig;
        $this->items = $items;
    }

    public function __invoke(Request $request) : Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            throw new NotFoundHttpException();
        }

        try {
            $item = $this->items->get($id);
        } catch (OutOfBoundsException $e) {
            throw new NotFoundHttpException('', $e);
        }

        return new Response($this->twig->render('item.html.twig', ['item' => $item]));
    }
}
