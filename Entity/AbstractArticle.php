<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Entity;

use Sonata\ArticleBundle\Model\AbstractArticle as BaseArticle;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
abstract class AbstractArticle extends BaseArticle
{
    /**
     * Updates dates before creating/updating entity.
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Updates dates before updating entity.
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
