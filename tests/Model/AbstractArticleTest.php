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

        static::assertSame($allStatuses, MockArticle::getStatuses());
        static::assertSame(array_keys($allStatuses), MockArticle::getValidStatuses());

        $contributorStatuses = [
            MockArticle::STATUS_DRAFT => 'Draft',
            MockArticle::STATUS_TO_PUBLISH => 'To publish',
            MockArticle::STATUS_ARCHIVED => 'Archived',
        ];

        static::assertSame($contributorStatuses, MockArticle::getContributorStatus());
    }

    public function testAbstractArticle(): void
    {
        $article = new MockArticle();

        static::assertInstanceOf(ArticleInterface::class, $article);
        static::assertInstanceOf(Collection::class, $article->getTags());
        static::assertInstanceOf(Collection::class, $article->getFragments());
        static::assertInstanceOf(Collection::class, $article->getCategories());
        static::assertSame(AbstractArticle::STATUS_DRAFT, $article->getStatus());
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

        static::assertSame(1, $article->getId());
        static::assertSame('Title', $article->getTitle());
        static::assertSame('Abstract', $article->getAbstract());
        static::assertSame('Subtitle', $article->getSubtitle());
        static::assertSame(1, $article->getStatus());
        static::assertSame($tags, $article->getTags());
        static::assertSame($categories, $article->getCategories());
        static::assertSame($media, $article->getMainImage());
        static::assertSame($validatedAt, $article->getValidatedAt());
        static::assertSame($publicationStartsAt, $article->getPublicationStartsAt());
        static::assertSame($publicationEndsAt, $article->getPublicationEndsAt());
        static::assertSame($createdAt, $article->getCreatedAt());
        static::assertSame($updatedAt, $article->getUpdatedAt());
        static::assertSame('Title', $article->__toString());
    }

    public function testIsValidated(): void
    {
        $article = new MockArticle();
        static::assertFalse($article->isValidated());

        $article->setValidatedAt(new \DateTime());
        static::assertTrue($article->isValidated());
    }

    public function testFragments(): void
    {
        $article = new MockArticle();
        $collection = $article->getFragments();

        $fragmentFoo = $this->createMock(AbstractFragment::class);
        $fragmentBar = $this->createMock(AbstractFragment::class);

        $newCollection = new ArrayCollection([$fragmentFoo, $fragmentBar]);
        $article->setFragments($newCollection);

        static::assertSame($collection, $article->getFragments());
        static::assertSame(2, $collection->count());
    }

    public function testFragmentAddRemove(): void
    {
        $article = new MockArticle();
        $collection = $article->getFragments();

        $fragment = $this->createMock(AbstractFragment::class);
        $fragment->expects(static::exactly(2))
            ->method('setArticle')
            ->withConsecutive([$article], [null]);

        $article->addFragment($fragment);

        static::assertSame(1, $collection->count());

        $article->removeFragment($fragment);

        static::assertSame(0, $collection->count());
    }

    public function testValidatorMetadata(): void
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects(static::once())
            ->method('addConstraint')
            ->with(static::callback(function ($parameter) {
                $this->assertInstanceOf(Callback::class, $parameter);
                $this->assertSame([AbstractArticle::class, 'validatorPublicationEnds'], $parameter->callback);

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
