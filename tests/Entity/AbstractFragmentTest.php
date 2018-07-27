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

        $this->assertInstanceOf(\Sonata\ArticleBundle\Model\AbstractFragment::class, $fragment);
    }

    public function testPrePersist(): void
    {
        $fragment = new MockFragment();

        $this->assertNull($fragment->getCreatedAt());
        $this->assertNull($fragment->getUpdatedAt());

        $fragment->prePersist();

        $this->assertInstanceOf(\DateTime::class, $fragment->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $fragment->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        $fragment = new MockFragment();

        $createdAt = new \DateTime();
        $fragment->setCreatedAt($createdAt);

        $fragment->preUpdate();

        $this->assertSame($createdAt, $fragment->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $fragment->getUpdatedAt());
        $this->assertNotSame($createdAt, $fragment->getUpdatedAt());
    }
}

class MockFragment extends AbstractFragment
{
    public function getId(): void
    {
    }
}
