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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
abstract class AbstractArticle implements ArticleInterface
{
    public const STATUS_DRAFT = 0;
    public const STATUS_TO_PUBLISH = 1;
    public const STATUS_PUBLISHED = 2;
    public const STATUS_ARCHIVED = 3;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface
     */
    protected $updatedAt;

    /**
     * @var \DateTimeInterface
     */
    protected $publicationStartsAt;

    /**
     * @var \DateTimeInterface
     */
    protected $publicationEndsAt;

    /**
     * @var int
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $subtitle;

    /**
     * @var string|null
     */
    protected $abstract;

    /**
     * @var \DateTimeInterface
     */
    protected $validatedAt;

    /**
     * @var ArrayCollection
     */
    protected $fragments;

    /**
     * @var Collection
     */
    protected $categories;

    /**
     * @var ArrayCollection
     */
    protected $tags;

    /**
     * @var MediaInterface|null
     */
    protected $mainImage;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->fragments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_TO_PUBLISH => 'To publish',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Gets statuses choices without publish status.
     */
    public static function getContributorStatus(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_TO_PUBLISH => 'To publish',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Returns keys of statuses (used for validation).
     */
    public static function getValidStatuses(): array
    {
        return array_keys(self::getStatuses());
    }

    public function setAbstract(?string $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function getAbstract(): ?string
    {
        return $this->abstract;
    }

    public function setCategories($categories = null): void
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setMainImage(MediaInterface $mainImage = null): void
    {
        $this->mainImage = $mainImage;
    }

    public function getMainImage(): ?MediaInterface
    {
        return $this->mainImage;
    }

    public function addFragment(FragmentInterface $fragment): void
    {
        if ($fragment instanceof ArticleFragmentInterface) {
            $fragment->setArticle($this);
        }
        $this->fragments->add($fragment);
    }

    public function removeFragment(FragmentInterface $fragment): void
    {
        if ($this->fragments->contains($fragment)) {
            $this->fragments->removeElement($fragment);
        }

        if ($fragment instanceof ArticleFragmentInterface) {
            $fragment->setArticle(null);
        }
    }

    public function setFragments($fragments = null): void
    {
        $this->fragments->clear();

        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }
    }

    public function getFragments()
    {
        return $this->fragments;
    }

    public function setPublicationEndsAt(\DateTimeInterface $publicationEndsAt = null): void
    {
        $this->publicationEndsAt = $publicationEndsAt;
    }

    public function getPublicationEndsAt(): ?\DateTimeInterface
    {
        return $this->publicationEndsAt;
    }

    public function setPublicationStartsAt(\DateTimeInterface $publicationStartsAt = null): void
    {
        $this->publicationStartsAt = $publicationStartsAt;
    }

    public function getPublicationStartsAt(): ?\DateTimeInterface
    {
        return $this->publicationStartsAt;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setTags($tags = null): void
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title ?: '';
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setValidatedAt(\DateTimeInterface $validatedAt = null): void
    {
        $this->validatedAt = $validatedAt;
    }

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function isValidated(): bool
    {
        return null !== $this->validatedAt;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new Assert\Callback([__NAMESPACE__.'\\AbstractArticle', 'validatorPublicationEnds']));
    }

    /**
     * Validation publication ends given by publication starts.
     */
    public static function validatorPublicationEnds(ArticleInterface $article, ExecutionContextInterface $context): void
    {
        if (\is_object($article->getPublicationStartsAt())
            && \is_object($article->getPublicationEndsAt())
            && $article->getPublicationStartsAt()->format('U') >= $article->getPublicationEndsAt()->format('U')
        ) {
            $context->buildViolation('article.publication_start_date_before_end_date')->addViolation();
        }
    }
}
