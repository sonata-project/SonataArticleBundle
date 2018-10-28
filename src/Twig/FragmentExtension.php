<?php

declare(strict_types=1);

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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentExtension extends AbstractExtension
{
    /**
     * @var FragmentHelper
     */
    protected $fragmentHelper;

    public function __construct(FragmentHelper $fragmentHelper)
    {
        $this->fragmentHelper = $fragmentHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sonata_article_render_fragment',
                [$this, 'renderFragment'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'sonata_article_render_article_fragments',
                [$this, 'renderArticleFragments'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName(): string
    {
        return 'fragment_extension';
    }

    public function renderFragment(FragmentInterface $fragment): string
    {
        return $this->fragmentHelper->render($fragment);
    }

    public function renderArticleFragments(ArticleInterface $article): string
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
