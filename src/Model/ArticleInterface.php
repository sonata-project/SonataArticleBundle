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

use Doctrine\Common\Collections\Collection;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\Tag;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
interface ArticleInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id): void;

    public function setAbstract(string $abstract): void;

    public function getAbstract(): ?string;

    /**
     * @param Category[]|Collection $categories
     */
    public function setCategories($categories = null): void;

    /**
     * @return Category[]|Collection
     */
    public function getCategories();

    public function setCreatedAt(\DateTimeInterface $createdAt): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setMainImage(MediaInterface $mainImage = null): void;

    public function getMainImage(): MediaInterface;

    public function addFragment(FragmentInterface $fragment): void;

    /**
     * @return FragmentInterface[]|Collection
     */
    public function getFragments();

    /**
     * @param FragmentInterface[]|Collection|null $fragments
     */
    public function setFragments($fragments = null): void;

    public function removeFragment(FragmentInterface $fragment): void;

    public function setPublicationEndsAt(\DateTimeInterface $publicationEndsAt = null): void;

    public function getPublicationEndsAt(): ?\DateTimeInterface;

    public function setPublicationStartsAt(\DateTimeInterface $publicationStartsAt = null): void;

    public function getPublicationStartsAt(): ?\DateTimeInterface;

    public function setStatus(int $status): void;

    public function getStatus(): int;

    public function setSubtitle(string $subtitle): void;

    public function getSubtitle(): ?string;

    /**
     * @param Tag[]|Collection $tags
     */
    public function setTags($tags = null): void;

    /**
     * @return Tag[]|Collection
     */
    public function getTags();

    public function setTitle(string $title): void;

    public function getTitle(): string;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setValidatedAt(\DateTimeInterface $validatedAt = null): void;

    public function getValidatedAt(): \DateTimeInterface;
}
