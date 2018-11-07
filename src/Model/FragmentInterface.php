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
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
interface FragmentInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    public function setCreatedAt(\DateTimeInterface $createdAt): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function setType(string $type): void;

    public function getType(): string;

    public function setFields(array $settings): void;

    /**
     * @param mixed $value
     */
    public function setField(string $name, $value): void;

    public function getFields(): array;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getField(string $name, $default = null);

    public function setPosition(?int $position): void;

    public function getPosition(): ?int;

    public function getBackofficeTitle(): string;

    public function setBackofficeTitle(string $backofficeTitle): void;
}
