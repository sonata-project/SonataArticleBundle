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

use Doctrine\Common\Collections\Collection;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\Tag;
use Sonata\MediaBundle\Model\Media;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
interface ArticleInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @param $abstract
     *
     * @return $this
     */
    public function setAbstract($abstract);

    /**
     * @return string $abstract
     */
    public function getAbstract();

    /**
     * @param Category[]|Collection $categories
     *
     * @return $this
     */
    public function setCategories(Collection $categories = null);

    /**
     * @return Category[]|Collection $categories
     */
    public function getCategories();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime $createdAt
     */
    public function getCreatedAt();

    /**
     * @param Media $mainImage
     *
     * @return $this
     */
    public function setMainImage(Media $mainImage = null);

    /**
     * @return Media $image
     */
    public function getMainImage();

    /**
     * @param FragmentInterface $fragment
     *
     * @return $this
     */
    public function addFragment(FragmentInterface $fragment);

    /**
     * @return FragmentInterface[]|Collection $fragments
     */
    public function getFragments();

    /**
     * @param FragmentInterface[]|Collection|null $fragments
     *
     * @return $this
     */
    public function setFragments($fragments = null);

    /**
     * @param FragmentInterface $fragment
     *
     * @return $this
     */
    public function removeFragment(FragmentInterface $fragment);

    /**
     * @param \DateTime $publicationEndsAt
     *
     * @return $this
     */
    public function setPublicationEndsAt(\DateTime $publicationEndsAt = null);

    /**
     * @return \DateTime $publicationStartsAt
     */
    public function getPublicationEndsAt();

    /**
     * @param \DateTime $publicationStartsAt
     *
     * @return $this
     */
    public function setPublicationStartsAt(\DateTime $publicationStartsAt = null);

    /**
     * @return \DateTime $a$publicationStartsAt
     */
    public function getPublicationStartsAt();

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return int $status
     */
    public function getStatus();

    /**
     * @param $subtitle
     *
     * @return $this
     */
    public function setSubtitle($subtitle);

    /**
     * @return string $subtitle
     */
    public function getSubtitle();

    /**
     * @param Tag[]|Collection $tags
     *
     * @return $this
     */
    public function setTags(Collection $tags = null);

    /**
     * @return Tag[]|Collection $tags
     */
    public function getTags();

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $validatedAt
     *
     * @return $this
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * @return \DateTime $validatedAt
     */
    public function getValidatedAt();
}
