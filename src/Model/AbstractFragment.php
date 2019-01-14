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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
abstract class AbstractFragment implements FragmentInterface, ArticleFragmentInterface
{
    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface
     */
    protected $updatedAt;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var int|null
     */
    protected $position;

    /**
     * @var string
     */
    protected $backofficeTitle;

    /**
     * @var ArticleInterface|null
     */
    protected $article;

    public function __toString(): string
    {
        return $this->getBackofficeTitle();
    }

    public function setArticle(ArticleInterface $article = null): void
    {
        $this->article = $article;
    }

    public function getArticle(): ?ArticleInterface
    {
        return $this->article;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setField(string $name, $value): void
    {
        $this->fields[$name] = $value;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $name, $default = null)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : $default;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getBackofficeTitle(): string
    {
        return $this->backofficeTitle ?: '';
    }

    public function setBackofficeTitle(string $backofficeTitle): void
    {
        $this->backofficeTitle = $backofficeTitle;
    }
}
