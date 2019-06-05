<?php

namespace App\Browser;

use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Link;

interface Browser
{
    public function request(string $method, string $uri) : PromiseInterface;

    public function click(Link $link) : PromiseInterface;

    public function submit(Form $form) : PromiseInterface;
}
