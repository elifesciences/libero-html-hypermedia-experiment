<?php

namespace App\JsonLd;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\TransferStats;
use ML\IRI\IRI;
use ML\JsonLD\DocumentLoaderInterface;
use ML\JsonLD\Exception\JsonLdException;
use ML\JsonLD\Processor;
use ML\JsonLD\RemoteDocument;
use function array_map;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function in_array;
use function key;
use function preg_match_all;
use function preg_split;
use function sprintf;
use function strpos;
use function substr;
use function substr_compare;
use function trim;

final class GuzzleLoader implements DocumentLoaderInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Load a (remote) document or context
     *
     * @param string $url The URL or path of the document to load.
     *
     * @return RemoteDocument The loaded document.
     *
     * @throws JsonLdException
     */
    public function loadDocument($url)
    {
        // if input looks like a file, try to retrieve it
        $input = trim($url);
        if (isset($input[0]) && (("{" === $input[0]) || ("[" === $input[0]))) {
            return new RemoteDocument($url, Processor::parse($input));
        }

        try {
            $response = $this->client->request(
                'GET',
                $url,
                [
                    'headers' => ['Accept' => 'application/ld+json, application/json; q=0.9, */*; q=0.1'],
                    'on_stats' => function (TransferStats $stats) use (&$effectiveUrl) {
                        $effectiveUrl = (string) $stats->getEffectiveUri();
                    },
                ]
            );
        } catch (GuzzleException $e) {
            throw new JsonLdException(
                JsonLdException::LOADING_DOCUMENT_FAILED,
                sprintf('Unable to load the remote document "%s".', $url),
                null, null, $e
            );
        }

        $remoteDocument = new RemoteDocument($effectiveUrl, null, $response->getHeaderLine('Content-Type'));

        $linkHeaderValues = $this->parseContextLinkHeaders($response->getHeader('Link'), new IRI($url));

        if (count($linkHeaderValues) === 1) {
            $remoteDocument->contextUrl = $linkHeaderValues[0];
        } elseif (count($linkHeaderValues) > 1) {
            throw new JsonLdException(
                JsonLdException::MULTIPLE_CONTEXT_LINK_HEADERS,
                'Found multiple contexts in HTTP Link headers'
            );
        }

        // If we got a media type, we verify it
        if ($remoteDocument->mediaType) {
            // Drop any media type parameters such as profiles
            if (false !== ($pos = strpos($remoteDocument->mediaType, ';'))) {
                $remoteDocument->mediaType = substr($remoteDocument->mediaType, 0, $pos);
            }

            $remoteDocument->mediaType = trim($remoteDocument->mediaType);

            if ('application/ld+json' === $remoteDocument->mediaType) {
                $remoteDocument->contextUrl = null;
            } elseif (('application/json' !== $remoteDocument->mediaType) &&
                (0 !== substr_compare($remoteDocument->mediaType, '+json', -5))) {
                throw new JsonLdException(
                    JsonLdException::LOADING_DOCUMENT_FAILED,
                    'Invalid media type',
                    $remoteDocument->mediaType
                );
            }
        }

        $remoteDocument->document = Processor::parse((string) $response->getBody());

        return $remoteDocument;
    }

    /**
     * Parse HTTP Link headers
     *
     * @param array $values An array of HTTP Link header values
     * @param IRI $baseIri The document's URL (used to expand relative URLs to absolutes)
     *
     * @return array An array of parsed HTTP Link headers
     */
    private function parseContextLinkHeaders(array $values, IRI $baseIri)
    {
        // Separate multiple links contained in a single header value
        for ($i = 0, $total = count($values); $i < $total; $i++) {
            if (strpos($values[$i], ',') !== false) {
                foreach (preg_split('/,(?=([^"]*"[^"]*")*[^"]*$)/', $values[$i]) as $v) {
                    $values[] = trim($v);
                }
                unset($values[$i]);
            }
        }

        $contexts = $matches = [];
        $trimWhitespaceCallback = function ($str) {
            return trim($str, "\"'  \n\t");
        };

        // Split the header in key-value pairs
        foreach ($values as $val) {
            $part = [];
            foreach (preg_split('/;(?=([^"]*"[^"]*")*[^"]*$)/', $val) as $kvp) {
                preg_match_all('/<[^>]+>|[^=]+/', $kvp, $matches);
                $pieces = array_map($trimWhitespaceCallback, $matches[0]);
                $part[$pieces[0]] = isset($pieces[1]) ? $pieces[1] : '';
            }

            if (in_array('http://www.w3.org/ns/json-ld#context', explode(' ', $part['rel']))) {
                $contexts[] = (string) $baseIri->resolve(trim(key($part), '<> '));
            }
        }

        return array_values(array_unique($contexts));
    }
}
