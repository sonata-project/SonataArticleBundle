Getting Started
===============

The bundle works on top of 2 simple models :
 * an ``Article``\ : An article is composed of Fragments and contains information about a content
   (status, type, publication date, categories, etc...)
 * a ``Fragment``\ : A fragment contains information about a part of the full content.
   Each fragment has its own template.


Rendering an article
--------------------

**This part is currently in development.**
The goal is to provide a simple Controller and Dynamic route which will fetch an article, then if all checks succeed,
display it for the user.

.. code-block:: php

    /**
     * Article index action
     *
     * @param Request $request
     * @param string  $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction(Request $request, $id)
    {
        /** @var Page $page */
        $page    = $this->getPage($request);
        $article = $this->entityManager->find(Article::class, $id);

        if (!$article) {
            throw new NotFoundHttpException(sprintf('No article found for ID "%s"', $id));
        }

        if ($article->getStatus() != Article::STATUS_PUBLISHED) {
            throw new NotFoundHttpException(sprintf('No article published found for ID "%s"', $id));
        }

        $now = new \DateTime();
        if ($article->getPublicationStartsAt() > $now
            || (!is_null($article->getPublicationEndsAt()) && $article->getPublicationEndsAt() < $now)) {
            throw new NotFoundHttpException(sprintf('No article found between publication dates for ID "%s"', $id));
        }

        $page->setTitle(sprintf('Articles - %s', $article->getTitle()));

        return $this->renderResponse($page, [
            'article' => $article,
            'page'    => $page,
        ]);
    }

    /**
     * @param PageInterface $page
     * @param array         $settings
     *
     * @return Response
     */
    public function renderResponse(PageInterface $page, array $settings = [])
    {
        $cms  = $this->cmsSelector->retrieve();
        $code = $page ? $cms->getCurrentPage()->getTemplateCode() : null;

        return new Response($this->templating->render(
            $this->templateManager->get($code)->getPath(),
            $settings
        ));
    }

    /**
     * Retrieves page associated with $request
     *
     * @param Request $request
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    public function getPage(Request $request)
    {
        $cms  = $this->cmsSelector->retrieve();
        $page = $cms->getCurrentPage();
        $slug = $request->get('slug');

        if (!$page && $slug) {
            throw new NotFoundHttpException('Sonata page not found. Add them from the admin.');
        }

        // This is required to avoid Sonata from stripping some params send to the twig
        $page->setDecorate(false);

        return $page;
    }

Render article fragments
------------------------

**This part is currently in development.**
SonataArticleBundle now comes with a twig helper which allows you to render article fragments
if they are enabled.


.. code-block:: jinja

    {{ sonata_article_render_article_fragments(article) }}


Or a specific fragment whether it is enabled or not.


.. code-block:: jinja

    {{ sonata_article_render_fragment(article.fragments[0]) }}


This extension is based on the FragmentHelper class so you can also render fragments directly
in the controller.


.. code-block:: php

    /**
     * Article index action
     *
     * @param Request $request
     * @param string  $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction(Request $request, $id)
    {
        $article = $this->entityManager->find(Article::class, $id);

        // ...

        $fragmentsRender = '';
        $fragmentsHelper = $this->get('sonata.article.helper.fragment');

        foreach ($article->getFragments() as $fragment) {
            if ($fragment->getEnabled()) {
                $fragmentsRender .= $this->renderFragment($fragment);
            }
        }

        // ...
    }
