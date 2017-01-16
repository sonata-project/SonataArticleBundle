<?php

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
use Sonata\MediaBundle\Model\Media;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
abstract class AbstractArticle implements ArticleInterface
{
    const STATUS_DRAFT = 0;
    const STATUS_TO_PUBLISH = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_ARCHIVED = 3;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $publicationStartsAt;

    /**
     * @var \DateTime
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
     * @var string
     */
    protected $subtitle;

    /**
     * @var string
     */
    protected $abstract;

    /**
     * @var \DateTime
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
     * @var Collection
     */
    protected $tags;

    /**
     * @var Media
     */
    protected $mainImage;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->fragments = new ArrayCollection();
    }

    /**
     * Returns title of article.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Gets statuses choices.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return array(
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_TO_PUBLISH => 'To publish',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        );
    }

    /**
     * Gets statuses choices without publish status.
     *
     * @return array
     */
    public static function getContributorStatus()
    {
        return array(
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_TO_PUBLISH => 'To publish',
            self::STATUS_ARCHIVED => 'Archived',
        );
    }

    /**
     * Returns keys of statuses (used for validation).
     *
     * @return array
     */
    public static function getValidStatuses()
    {
        return array_keys(self::getStatuses());
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategories(Collection $categories = null)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainImage(Media $mainImage = null)
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * {@inheritdoc}
     */
    public function addFragment(FragmentInterface $fragment)
    {
        $fragment->setArticle($this);
        $this->fragments[] = $fragment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeFragment(FragmentInterface $fragment)
    {
        if ($this->fragments->contains($fragment)) {
            $this->fragments->removeElement($fragment);
        }
        $fragment->setArticle(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFragments($fragments = null)
    {
        $this->fragments = new ArrayCollection();

        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublicationEndsAt(\DateTime $publicationEndsAt = null)
    {
        $this->publicationEndsAt = $publicationEndsAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicationEndsAt()
    {
        return $this->publicationEndsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublicationStartsAt(\DateTime $publicationStartsAt = null)
    {
        $this->publicationStartsAt = $publicationStartsAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicationStartsAt()
    {
        return $this->publicationStartsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setTags(Collection $tags = null)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidatedAt(\DateTime $validatedAt = null)
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Validation.
     *
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Assert\Callback(array(__NAMESPACE__.'\\AbstractArticle', 'validatorPublicationEnds')));
    }

    /**
     * Validation publication ends given by publication starts.
     *
     * @param ArticleInterface          $article
     * @param ExecutionContextInterface $context
     */
    public static function validatorPublicationEnds(ArticleInterface $article, ExecutionContextInterface $context)
    {
        if (is_object($article->getPublicationStartsAt())
            && is_object($article->getPublicationEndsAt())
            && $article->getPublicationStartsAt()->format('U') >= $article->getPublicationEndsAt()->format('U')
        ) {
            $context->buildViolation('article.publication_start_date_before_end_date')->addViolation();
        }
    }
}
