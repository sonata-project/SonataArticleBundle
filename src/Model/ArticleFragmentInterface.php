<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Model;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
interface ArticleFragmentInterface
{
    /**
     * @return ArticleInterface
     */
    public function getArticle();

    /**
     * @param ArticleInterface $article
     *
     * @return $this
     */
    public function setArticle(ArticleInterface $article = null);
}
