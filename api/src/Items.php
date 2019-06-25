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
            'content' => <<<HTML
<p class="paragraph"><strong>By Giuliano Maciocci, Emmy Tsang, Nokome Bentley and Michael Aufreiter</strong></p>

                            <p class="paragraph">In February, eLife introduced its <a href="https://elifesci.org/reproducible-example">first computationally reproducible document</a>, based on a research article originally published in the Reproducibility Project: Cancer Biology by Tim Errington, the Director of Research at the Center for Open Science. The interactive article is a demonstration of some of the capabilities of the initial prototype of the <a href="https://elifesciences.org/labs/7dbeb390/reproducible-document-stack-supporting-the-next-generation-research-article">Reproducible Document Stack (RDS)</a>, an open-source tool stack for authoring and publishing reproducible articles developed by <a href="http://substance.io">Substance</a> and building on technology from <a href="http://stenci.la">Stencila</a> and <a href="https://mybinder.org/">Binder</a>. The demo also showcased eLife’s vision for the future of research articles.</p>

                            <p class="paragraph">The research community’s response to the article was overwhelmingly encouraging: thousands of researchers explored the paper’s in-line code block re-execution abilities by manipulating its plots, and several authors approached us directly to ask how they might publish a reproducible version of their own manuscripts.</p>

                            <figure class="captioned-asset">

    <picture class="captioned-asset__picture">
        <source srcset="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage1_0.png/full/1234,/0/default.webp 2x, https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage1_0.png/full/617,/0/default.webp 1x"
            type="image/webp"
            >
        <source srcset="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage1_0.png/full/1234,/0/default.jpg 2x, https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage1_0.png/full/617,/0/default.jpg 1x"
            type="image/jpeg"
            >
        <img src="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage1_0.png/full/617,/0/default.jpg"
             
             alt="Changing plots on the RDS demo"
             class="captioned-asset__image"
        >
    </picture>




    <figcaption class="captioned-asset__caption">

        <h6 class="caption-text__heading">Changing the plot on the right from a bar plot to a dot plot by in-browser R-code re-execution.</h6>
      
      



    </figcaption>




</figure>

                            <p class="paragraph">Encouraged by the community interest and feedback, we have now started working on achieving a scalable implementation and service infrastructure to support the publication of reproducible articles. The goal of this next phase in the RDS project is to ship researcher-centred open-source solutions that will allow for the hosting and publication of reproducible documents, at scale, by anyone. This includes building conversion, rendering and authoring tools, and the backend infrastructure needed to execute reproducible articles in the browser.</p>

                            <p class="paragraph">Interoperability, modularity and openness are at the heart of the RDS’s design and development. We want authors, readers and publishers from different research communities to be able to use and interact with these tools seamlessly. RDS will continue to be developed open-source, and we strive to update and engage the community at all stages of development. Our first priority will be enabling interoperability with existing authoring tools for the Jupyter and R Markdown communities.</p>

                            <blockquote class="pull-quote">
  <p class="paragraph">Interoperability, modularity and openness are at the heart of the RDS’s design and development.</p>

  
</blockquote>

                            <p class="paragraph">As a first step, eLife aims to publish reproducible articles as companions of already accepted papers. We will endeavour to accept submissions of reproducible manuscripts in the form of <a href="https://github.com/substance/dar">DAR files</a> by the end of 2019.</p>

                            <p class="paragraph">We outline the key areas of innovation of this next phase of RDS development below.</p>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">Format converters for reproducible documents</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph">Despite the fact that most research articles are still written in Word, an increasing number of scientists are moving to more reproducible formats such as Jupyter Notebooks and R Markdown. The new DAR format for reproducible documents finally offers a way to take this reproducibility through to publication; but without publisher support or any easy way to convert between formats, researchers fall back to converting their work to Word or PDFs before submitting to publishers, thereby losing reproducibility.</p>
<p class="paragraph">Interoperability is key when working with scientists across disciplines. We want it to be easy for a scientist to create a reproducible document from multiple starting points. Format converters will allow researchers using Jupyter Notebooks and R Markdown to submit reproducible articles to eLife and have them converted to the DAR format, without losing the reproducible elements.</p>
<blockquote class="pull-quote">
  <p class="paragraph">We want it to be easy for a scientist to create a reproducible document from multiple starting points.</p>

  
</blockquote>




  </div>

</section>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">Reproducible execution services and supporting tools development</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph">To publish reproducible documents, publishers and researchers need reliable and performant reproducible execution environments as the backend for running live-code elements. <a href="https://github.com/stencila/hub">Stencila Hub</a> is being built to provide such environments, and for the next phase of RDS development, additional functionality for the Hub will be specifically designed by Stencila for the publisher use case. The aim is to build a robust and scalable software and personnel infrastructure for the provision of execution services in support of the RDS, as well as to enhance the overall user experience. Stencila will provide the reproducible execution services to eLife, and subsequently to any publishers interested in making RDS publications available to their customers, through a Service Level Agreement.</p>
<p class="paragraph">We greatly value existing technology and the ongoing efforts of the open-source community, but at the same time, it is important that we encourage innovation and support the exploration of new approaches and solutions. We aim to maximise our reuse of existing open-source software, such as Jupyter kernels, Kubernetes, Jupyter Hub and Binder, to deliver execution services robustly and cost-effectively. Stencila’s short-term plan is to deploy its own BinderHub instance for the hosting of eLife’s reproducible articles; if alternative implementations are warranted, we will conduct thorough research and comparisons on user experience and performance, and deliver any alternative implementations as complements to existing technologies.</p>
<p class="paragraph">We also believe that there is still considerable room for innovation in the arena of efficient and scalable reproducible computation, and that a joint effort between the eLife and Stencila communities will facilitate the exploration of more robust publishing infrastructures. For example, Docker images, which are currently used to build reproducible research environments, can be very large. Stencila will therefore explore solutions to optimise the size of Docker images to the bare minimum required by a reproducible document, with the end goal of producing well-documented, tested and modular tools that will enable more reproducible and efficient execution environments. We hope to contribute these open tools and ideas towards other publishing and reproducibility pipelines beyond eLife and the RDS project.</p>




  </div>

</section>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">File format specification for portable reproducible documents</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph"><a href="https://elifesciences.org/labs/8de87c33/texture-an-open-science-manuscript-editor">Texture</a> is an open-source editing software designed and developed by Substance specifically to edit and annotate scientific content. It uses the <a href="https://github.com/substance/dar">DAR</a> format, which is based on a stricter form of JATS-XML. To improve the portability of reproducible documents, Substance will extend the DAR file format specification to natively support R Markdown’s inline code cells, giving the DAR file format greater interoperability with mainstream tools for computational reproducibility.</p>




  </div>

</section>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">A Stencila plugin for Texture</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph">Substance will also work on improving the Texture authoring client by extending it with a new extension architecture that will allow plugins, such as one encompassing Stencila’s code-authoring functionality, to be added into the Texture client. This will ensure Texture can be maintained as primarily an XML editor, for use in the authoring and production of traditional manuscripts, but also extended as needed via separately maintained plugins to encompass new functionality such as Reproducible Document authoring and execution. A reference Stencila plugin will also be created as the first to use the new plugin architecture.</p>
<figure class="captioned-asset">

    <picture class="captioned-asset__picture">
        <source srcset="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage2_0.png/full/617,/0/default.webp"
            type="image/webp"
            >
        <source srcset="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage2_0.png/full/617,/0/default.jpg"
            type="image/jpeg"
            >
        <img src="https://iiif.elifesciences.org/journal-cms/labs-post-content%2F2019-05%2Fimage2_0.png/full/617,/0/default.jpg"
             
             alt="Researchers want to submit code and data"
             class="captioned-asset__image"
        >
    </picture>




    <figcaption class="captioned-asset__caption">

        <h6 class="caption-text__heading">In 2017, <a href="https://elifesciences.org/inside-elife/e832444e/innovation-understanding-the-demand-for-reproducible-research-articles">researchers familiar with reproducible document tools</a> told us they were interested in being able to share and read research articles with features that support better code and data sharing as well as greater interactivity and executability.</h6>
      
      



    </figcaption>




</figure>




  </div>

</section>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">Authoring and publishing a reproducible article: the future workflow</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph">Once the technical work on the next phase of the RDS project is complete, we envision the following workflow for authoring and publishing reproducible articles:</p>

    <ol class="list list--number">
            <li><strong>Authoring. </strong>The author will follow provided guidelines for how to write reproducible articles as R Markdown, Jupyter Notebook or DAR documents. These guidelines will include how to add the necessary metadata to each of these formats.</li>
            <li><strong>Uploading. </strong>The author uploads their article, and necessary data and code files, to a “project” on Stencila Hub. Eventually this step may be folded into a publisher’s submission workflow.</li>
            <li><strong>Building.</strong> A compact and efficient reproducible execution environment is built for the article based on the software packages used in it. These tools also create a manifest of the software dependencies of the article, thereby providing data for software citations.</li>
            <li><strong>Verification. </strong>The article is executed headlessly on the Stencila Hub within the reproducible execution environment to verify that it is indeed reproducible.</li>
            <li><strong>Conversion. </strong>Once the article has been verified as being reproducible, the author presses a “Create DAR” button (when not already using the format) to export their article to DAR ready for eLife’s production team. Any issues with conversion would be reported to the user so that they could make corrections to their original document.</li>
            <li><strong>Publication. </strong>A reproducible companion version of the article can be made available to readers via two mechanisms:</li>
            <li>The article is converted to HTML and served from the Stencila Hub. It is progressively enhanced using Javascript to make the reproducible elements live by connecting to the execution environment built for the article (also hosted on the Hub).</li>
            <li>The DAR is rendered by Javascript within the browser using a Texture Reader interface that is hosted by eLife and which connects to the execution environment built for the article and hosted on the Hub (this is the setup used in the demo).</li>
    </ol>






  </div>

</section>

                            <section
    class="article-section "
  
  
  
>

  <header class="article-section__header">
    <h3 class="article-section__header_text">Get involved</h3>
  </header>

  <div class="article-section__body">
      <p class="paragraph">Since the release of eLife’s first reproducible article, we have been actively collecting feedback from both the research and the open-source community, and this has been crucial to shaping the development of the RDS.</p>
<p class="paragraph">We strive to continue this for the next phase of the project, which is expected to last for about a year. We hope to post frequent updates on the milestones here on <a href="http://elifesciences.org/labs">eLife Labs</a>, and we welcome your input and feedback throughout, whether about the concept or specific technical elements. Please annotate publicly on this and future blog posts.</p>
<p class="paragraph">If you'd like to know more about the RDS project, or are a researcher or developer wishing to contribute to the project, here are some key resources to get you started:</p>


    <ul class="list list--bullet">
            <li>Stencila Desktop <a href="https://stenci.la/use/install.html">installation instructions</a> and <a href="https://github.com/stencila/desktop">code repository</a>.</li>
            <li><a href="https://github.com/substance/texture">Texture repository</a>, the open-source manuscript editor that Stencila Desktop is based on.</li>
            <li>See the <a href="https://stenci.la/learn/integrations/converters.html">guide on the Stencila Converters</a> and the <a href="https://github.com/stencila/convert">relevant repository</a>.</li>
            <li>Join <a href="https://gitter.im/stencila/stencila">Stencila’s chat channel</a> and <a href="https://community.stenci.la/">Community Forum</a>.</li>
            <li>Sign up for the <a href="https://crm.elifesciences.org/crm/RDS-updates">RDS community newsletter</a>.</li>
            <li>Join <a href="https://zoom.us/webinar/register/WN_mnVgZtNEQXezMtDtSz6VaA">Stencila’s upcoming community call</a> on June 3 (1pm PST), and eLife’s open-source community call on June 24.</li>
    </ul>

<p class="paragraph">We will discuss the RDS project and reproducibility in general at various conferences over the next two months:</p>


    <ul class="list list--bullet">
            <li><a href="https://cascadiarconf.com/">Cascadia R Conf</a> – June 8, Bellevue, US (Stencila).</li>
            <li><a href="https://escience.washington.edu/events/writing-reproducible-executable-scientific-papers-with-r-python-a-hands-on-workshop/">Writing reproducible &amp; executable scientific papers with R &amp; Python: a hands-on workshop</a> – June 10–11, Seattle, US (Stencila).</li>
            <li>“Hack and Yack” session at the British Library – June 17, London, UK (eLife).</li>
            <li><a href="https://indico.cern.ch/event/786048/timetable/#20190619.detailed">OAI11 UNIGE-CERN Workshop on Innovations in Scholarly Communications</a> – June 19–21, Geneva, Switzerland (eLife).</li>
            <li><a href="https://www.software.ac.uk/ccmcr19">CarpentryConnect 2019</a> – June 25–27, Manchester, UK (eLife).</li>
            <li><a href="https://www.iscb.org/ismbeccb2019">ISCB/ECCB/BOSC 2019</a> – July 21–25, Basel, Switzerland (eLife).</li>
    </ul>

<p class="paragraph">If you have specific questions or comments, you can also email us at innovation [at] elifesciences [dot] org, or interact with us on Twitter <a href="http://twitter.com/eLifeInnovation">@eLifeInnovation</a>.</p>
<p class="paragraph">#</p>
<p class="paragraph">Do you have an idea or innovation to share? Send a short outline for a Labs blogpost to innovation [at] elifesciences [dot] org.</p>
<p class="paragraph">Are you interested in contributing to open-source projects like the Reproducible Document Stack to drive forward open science? Applications are open until June 2 for the <a href="http://sprint.elifesciences.org">eLife Innovation Sprint in September 2019</a>.</p>
<p class="paragraph">For the latest in innovation, eLife Labs and new open-source tools, sign up for our <a href="https://crm.elifesciences.org/crm/tech-news?utm_source=Labs-RDS2&amp;utm_medium=website&amp;utm_campaign=technews">technology and innovation newsletter</a>. You can also follow <a href="http://twitter.com/eLifeInnovation">@eLifeInnovation</a> on Twitter.</p>
HTML
            ,
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
