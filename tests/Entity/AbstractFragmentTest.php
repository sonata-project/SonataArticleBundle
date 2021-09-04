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
use Sonata\ArticleBundle\Entity\AbstractFragment;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractFragmentTest extends TestCase
{
    public function testAbstractFragment(): void
    {
        $fragment = new MockFragment();

        static::assertInstanceOf(AbstractFragment::class, $fragment);
    }

    public function testPrePersist(): void
    {
        $fragment = new MockFragment();

        static::assertNull($fragment->getCreatedAt());
        static::assertNull($fragment->getUpdatedAt());

        $fragment->prePersist();

        static::assertInstanceOf(\DateTimeInterface::class, $fragment->getCreatedAt());
        static::assertInstanceOf(\DateTimeInterface::class, $fragment->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        $fragment = new MockFragment();

        $createdAt = new \DateTime();
        $fragment->setCreatedAt($createdAt);

        $fragment->preUpdate();

        static::assertSame($createdAt, $fragment->getCreatedAt());
        static::assertInstanceOf(\DateTimeInterface::class, $fragment->getUpdatedAt());
        static::assertNotSame($createdAt, $fragment->getUpdatedAt());
    }
}

class MockFragment extends AbstractFragment
{
    public function getId(): void
    {
    }

    public function setId($id): void
    {
    }
}
