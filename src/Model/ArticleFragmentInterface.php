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

namespace Sonata\ArticleBundle\Model;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
interface ArticleFragmentInterface
{
    public function getArticle(): ArticleInterface;

    public function setArticle(?ArticleInterface $article = null): void;
}
