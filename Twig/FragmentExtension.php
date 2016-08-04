<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Twig;

use Sonata\ArticleBundle\Helper\FragmentHelper;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentExtension extends \Twig_Extension
{
    /**
     * @var FragmentHelper
     */
    protected $fragmentHelper;

    /**
     * @param FragmentHelper $fragmentHelper
     */
    public function __construct(FragmentHelper $fragmentHelper)
    {
        $this->fragmentHelper = $fragmentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'sonata_article_render_fragment',
                array($this, 'renderFragment'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction(
                'sonata_article_render_article_fragments',
                array($this, 'renderArticleFragments'),
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fragment_extension';
    }

    /**
     * @param FragmentInterface $fragment
     *
     * @return string
     */
    public function renderFragment(FragmentInterface $fragment)
    {
        return $this->fragmentHelper->render($fragment);
    }

    /**
     * @param ArticleInterface $article
     *
     * @return string
     */
    public function renderArticleFragments(ArticleInterface $article)
    {
        $output = '';

        foreach ($article->getFragments() as $fragment) {
            if ($fragment->getEnabled()) {
                $output .= $this->renderFragment($fragment);
            }
        }

        return $output;
    }
}
