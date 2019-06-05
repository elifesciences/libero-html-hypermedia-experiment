Libero HTML Hypermedia test
===========================

This is an experiment to use a hypermedia API using HTML5 rather than a JSON- or XML-based structure (eg [HAL](https://tools.ietf.org/html/draft-michaud-xml-hal-02)).

Running it
----------

1. Run `docker-compose up`

2. Open http://localhost:8081/ in your browser. This is the API, but as it's HTML you can view it easily. It contains a list of the available content, and a form to access a piece of content by its ID.

3. Open http://localhost:8080/ in your browser. This is the website that reads data from the API. It reads the API index and follows the link to get content. You can see HTTP requests being made in the web profiler bar.

Possible benefits
-----------------

- Natively hypermedia (as opposed to following some standard to turn a format into one).
- Mature and well understood.
- Viewable in your browser, so could be its own documentation (ie embed documentation right next to the actual data).
- Has forms for performing actions (rather than having to know what to `POST` to a URI etc).
- Potential of being able to reuse vocabularies like Schema.org rather than having to make standards up.

Downsides
---------

- It's ununsual. (Both HTML as an API format, and hypermedia itself.)
- HTML is verbose for machine reading (but can be mitigated by use of the [`Prefer` header](https://tools.ietf.org/html/rfc7240).
- Following links makes it more chatty.
  - Caching can help. (Including [`stale-while-revalidate`](https://tools.ietf.org/html/rfc5861).)
  - Could embed partial/complete targets (so the link is to a fragment rather than different URI).
- Still needs some standard on _what_ to parse/follow.
  - Tried [Microdata](https://www.w3.org/TR/microdata/) (+Schema.org), works but can't contain HTML fragments (becomes plain text). Might not be a problem if the actual content is inside JATS or something.
  - Tried [HTML+RDFa Lite](https://www.w3.org/TR/html-rdfa/) (+Schema.org), also works but also can't contain HTML fragments.
  - Wanted to try [HTML+RDFa Core](https://www.w3.org/TR/html-rdfa/) which _can_ contain [HTML content](https://www.w3.org/TR/rdf11-concepts/#section-html), but support doesn't seem to exist in any PHP library.
  - Can make up own standard of identifiers, classes etc (any maybe use something like [ALPS](https://tools.ietf.org/html/draft-amundsen-richardson-foster-alps-02) to document), but means inventing stuff again...

Reading
-------

- http://shop.oreilly.com/product/0636920020530.do
- http://codeartisan.blogspot.com/2012/07/using-html-as-media-type-for-your-api.html
- https://fideloper.com/description-of-hypermedia-apis
- http://smizell.com/weblog/2014/html-hypermedia-api-decoupled-ui
- https://blog.jayway.com/2012/08/01/combining-html-hypermedia-apis-and-adaptive-web-design/
