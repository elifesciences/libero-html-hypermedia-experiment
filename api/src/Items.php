<?php

namespace App;

use IteratorAggregate;
use OutOfBoundsException;
use Traversable;

final class Items implements IteratorAggregate
{
    private static $items = [
        [
            'id' => '09560',
            'doi' => '10.7554/eLife.09560',
            'type' => 'http://schema.org/ScholarlyArticle',
            'title' => '<i>Homo naledi</i>, a new species of the genus <i>Homo</i> from the Dinaledi Chamber, South Africa',
            'jats' => 'https://github.com/elifesciences/elife-article-xml/raw/master/articles/elife-09560-v1.xml',
        ],
        [
            'id' => '24231',
            'doi' => '10.7554/eLife.24231',
            'type' => 'http://schema.org/ScholarlyArticle',
            'title' => 'The age of <i>Homo naledi</i> and associated sediments in the Rising Star Cave, South Africa',
            'jats' => 'https://github.com/elifesciences/elife-article-xml/raw/master/articles/elife-24231-v1.xml',
        ],
        [
            'id' => 'b521cf4d',
            'title' => 'Reproducible Document Stack: towards a scalable solution for reproducible articles',
            'type' => 'http://schema.org/BlogPosting',
            'description' => 'We announce our roadmap towards an open, scalable infrastructure for the publication of computationally reproducible articles.',
            'datePublished' => '2019-05-22',
        ],
    ];

    public function get(string $id) : array
    {
        foreach ($this as $item) {
            if ($id === $item['id']) {
                return $item;
            }
        }

        throw new OutOfBoundsException("Unknown ID {$id}");
    }

    public function getIterator() : Traversable
    {
        yield from self::$items;
    }
}
