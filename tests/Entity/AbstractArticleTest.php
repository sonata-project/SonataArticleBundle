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

namespace Sonata\ArticleBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\Entity\AbstractArticle;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractArticleTest extends TestCase
{
    public function testAbstractArticle(): void
    {
        $article = new MockArticle();

        $this->assertInstanceOf(AbstractArticle::class, $article);
    }

    public function testPrePersist(): void
    {
        $article = new MockArticle();

        $this->assertNull($article->getCreatedAt());
        $this->assertNull($article->getUpdatedAt());

        $article->prePersist();

        $this->assertInstanceOf(\DateTimeInterface::class, $article->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $article->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        $article = new MockArticle();

        $createdAt = new \DateTime();
        $article->setCreatedAt($createdAt);

        $article->preUpdate();

        $this->assertSame($createdAt, $article->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $article->getUpdatedAt());
        $this->assertNotSame($createdAt, $article->getUpdatedAt());
    }
}

class MockArticle extends AbstractArticle
{
    public function getId(): void
    {
    }

    public function setId($id): void
    {
    }
}
