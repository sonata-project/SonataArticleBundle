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
     *
     * @return $this
     */
    public function setId($id);

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
     * @param $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled);

    /**
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return string $type
     */
    public function getType();

    /**
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings($settings);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSetting($name, $value);

    /**
     * @return string $type
     */
    public function getSettings();

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return string $type
     */
    public function getSetting($name, $default = null);

    /**
     * @param $position
     *
     * @return $this
     */
    public function setPosition($position);

    /**
     * @return int $position
     */
    public function getPosition();
}
