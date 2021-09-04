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

namespace Sonata\ArticleBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\Model\AbstractFragment;
use Sonata\ArticleBundle\Model\ArticleFragmentInterface;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractFragmentTest extends TestCase
{
    public function testAbstractFragment(): void
    {
        $fragment = new MockFragment();

        static::assertInstanceOf(FragmentInterface::class, $fragment);
        static::assertInstanceOf(ArticleFragmentInterface::class, $fragment);
        static::assertTrue($fragment->getEnabled());
    }

    public function testProperties(): void
    {
        $fragment = new MockFragment();

        $article = $this->createMock(ArticleInterface::class);
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $fragment->setId(1);
        $fragment->setType('foo');
        $fragment->setBackofficeTitle('Foo Fragment');
        $fragment->setEnabled(false);
        $fragment->setPosition(1);
        $fragment->setArticle($article);
        $fragment->setFields(['foo']);
        $fragment->setCreatedAt($createdAt);
        $fragment->setUpdatedAt($updatedAt);

        static::assertSame(1, $fragment->getId());
        static::assertSame('foo', $fragment->getType());
        static::assertSame('Foo Fragment', $fragment->getBackofficeTitle());
        static::assertFalse($fragment->getEnabled());
        static::assertSame(1, $fragment->getPosition());
        static::assertSame(['foo'], $fragment->getFields());
        static::assertSame($article, $fragment->getArticle());
        static::assertSame($createdAt, $fragment->getCreatedAt());
        static::assertSame($updatedAt, $fragment->getUpdatedAt());
        static::assertSame('Foo Fragment', $fragment->__toString());
    }

    public function testSettings(): void
    {
        $fragment = new MockFragment();

        $fragment->setField('foo', 'bar');

        static::assertSame(['foo' => 'bar'], $fragment->getFields());
        static::assertSame('bar', $fragment->getField('foo'));
        static::assertNull($fragment->getField('undefined'));
        static::assertSame('baz', $fragment->getField('undefined-with-default', 'baz'));
    }
}

class MockFragment extends AbstractFragment
{
    private $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
}
