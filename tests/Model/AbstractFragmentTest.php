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

        $this->assertInstanceOf(FragmentInterface::class, $fragment);
        $this->assertInstanceOf(ArticleFragmentInterface::class, $fragment);
        $this->assertTrue($fragment->getEnabled());
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

        $this->assertEquals(1, $fragment->getId());
        $this->assertEquals('foo', $fragment->getType());
        $this->assertEquals('Foo Fragment', $fragment->getBackofficeTitle());
        $this->assertFalse($fragment->getEnabled());
        $this->assertEquals(1, $fragment->getPosition());
        $this->assertEquals(['foo'], $fragment->getFields());
        $this->assertSame($article, $fragment->getArticle());
        $this->assertSame($createdAt, $fragment->getCreatedAt());
        $this->assertSame($updatedAt, $fragment->getUpdatedAt());
        $this->assertEquals('Foo Fragment', $fragment->__toString());
    }

    public function testSettings(): void
    {
        $fragment = new MockFragment();

        $fragment->setField('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $fragment->getFields());
        $this->assertEquals('bar', $fragment->getField('foo'));
        $this->assertNull($fragment->getField('undefined'));
        $this->assertEquals('baz', $fragment->getField('undefined-with-default', 'baz'));
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
