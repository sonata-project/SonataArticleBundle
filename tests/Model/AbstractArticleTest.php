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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\Model\AbstractArticle;
use Sonata\ArticleBundle\Model\AbstractFragment;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractArticleTest extends TestCase
{
    public function testAbstractArticleStatuses(): void
    {
        $allStatuses = [
            MockArticle::STATUS_DRAFT => 'Draft',
            MockArticle::STATUS_TO_PUBLISH => 'To publish',
            MockArticle::STATUS_PUBLISHED => 'Published',
            MockArticle::STATUS_ARCHIVED => 'Archived',
        ];

        $this->assertEquals($allStatuses, MockArticle::getStatuses());
        $this->assertEquals(array_keys($allStatuses), MockArticle::getValidStatuses());

        $contributorStatuses = [
            MockArticle::STATUS_DRAFT => 'Draft',
            MockArticle::STATUS_TO_PUBLISH => 'To publish',
            MockArticle::STATUS_ARCHIVED => 'Archived',
        ];

        $this->assertEquals($contributorStatuses, MockArticle::getContributorStatus());
    }

    public function testAbstractArticle(): void
    {
        $article = new MockArticle();

        $this->assertInstanceOf(ArticleInterface::class, $article);
        $this->assertInstanceOf(Collection::class, $article->getTags());
        $this->assertInstanceOf(Collection::class, $article->getFragments());
        $this->assertInstanceOf(Collection::class, $article->getCategories());
        $this->assertEquals(AbstractArticle::STATUS_DRAFT, $article->getStatus());
    }

    public function testProperties(): void
    {
        $article = new MockArticle();

        $media = $this->createMock(MediaInterface::class);

        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();
        $validatedAt = new \DateTime();
        $publicationStartsAt = new \DateTime();
        $publicationEndsAt = new \DateTime();
        $categories = new ArrayCollection();
        $tags = [];

        $article->setId(1);
        $article->setTitle('Title');
        $article->setAbstract('Abstract');
        $article->setSubtitle('Subtitle');
        $article->setStatus(1);
        $article->setCategories($categories);
        $article->setTags($tags);
        $article->setMainImage($media);
        $article->setValidatedAt($validatedAt);
        $article->setPublicationStartsAt($publicationStartsAt);
        $article->setPublicationEndsAt($publicationEndsAt);
        $article->setCreatedAt($createdAt);
        $article->setUpdatedAt($updatedAt);

        $this->assertEquals(1, $article->getId());
        $this->assertEquals('Title', $article->getTitle());
        $this->assertEquals('Abstract', $article->getAbstract());
        $this->assertEquals('Subtitle', $article->getSubtitle());
        $this->assertEquals(1, $article->getStatus());
        $this->assertEquals($tags, $article->getTags());
        $this->assertSame($categories, $article->getCategories());
        $this->assertSame($media, $article->getMainImage());
        $this->assertSame($validatedAt, $article->getValidatedAt());
        $this->assertSame($publicationStartsAt, $article->getPublicationStartsAt());
        $this->assertSame($publicationEndsAt, $article->getPublicationEndsAt());
        $this->assertSame($createdAt, $article->getCreatedAt());
        $this->assertSame($updatedAt, $article->getUpdatedAt());
        $this->assertEquals('Title', $article->__toString());
    }

    public function testIsValidated(): void
    {
        $article = new MockArticle();
        $this->assertFalse($article->isValidated());

        $article->setValidatedAt(new \DateTime());
        $this->assertTrue($article->isValidated());
    }

    public function testFragments(): void
    {
        $article = new MockArticle();
        $collection = $article->getFragments();

        $fragmentFoo = $this->createMock(AbstractFragment::class);
        $fragmentBar = $this->createMock(AbstractFragment::class);

        $newCollection = new ArrayCollection([$fragmentFoo, $fragmentBar]);
        $article->setFragments($newCollection);

        $this->assertSame($collection, $article->getFragments());
        $this->assertEquals(2, $collection->count());
    }

    public function testFragmentAddRemove(): void
    {
        $article = new MockArticle();
        $collection = $article->getFragments();

        $fragment = $this->createMock(AbstractFragment::class);
        $fragment->expects($this->exactly(2))
            ->method('setArticle')
            ->withConsecutive([$article], [null]);

        $article->addFragment($fragment);

        $this->assertEquals(1, $collection->count());

        $article->removeFragment($fragment);

        $this->assertEquals(0, $collection->count());
    }

    public function testValidatorMetadata(): void
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects($this->once())
            ->method('addConstraint')
            ->with($this->callback(function ($parameter) {
                $this->assertInstanceOf(Callback::class, $parameter);
                $this->assertEquals([AbstractArticle::class, 'validatorPublicationEnds'], $parameter->callback);

                return true;
            }));

        AbstractArticle::loadValidatorMetadata($classMetadata);
    }

    /**
     * @dataProvider validatorPublicationProvider
     */
    public function testValidatorPublication($startAt, $endsAt, $expectation): void
    {
        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintBuilder->expects($this->$expectation())
            ->method('addViolation');

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects($this->$expectation())
            ->method('buildViolation')
            ->with('article.publication_start_date_before_end_date')
            ->willReturn($constraintBuilder);

        $article = new MockArticle();
        $article->setPublicationStartsAt($startAt);
        $article->setPublicationEndsAt($endsAt);

        AbstractArticle::validatorPublicationEnds($article, $executionContext);
    }

    public function validatorPublicationProvider(): array
    {
        return [
            [null, null, 'never'],
            [null, new \DateTime('2018-07-02 00:00:00'), 'never'],
            [new \DateTime('2018-07-01 00:00:00'), null, 'never'],
            [new \DateTime('2018-07-01 00:00:00'), new \DateTime('2018-07-02 00:00:00'), 'never'],
            [new \DateTime('2018-07-01 00:00:00'), new \DateTime('2018-07-01 00:00:00'), 'once'],
            [new \DateTime('2018-07-02 00:00:00'), new \DateTime('2018-07-01 00:00:00'), 'once'],
        ];
    }
}

class MockArticle extends AbstractArticle
{
    private $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
}
