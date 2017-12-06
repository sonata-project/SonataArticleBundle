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

namespace Sonata\ArticleBundle\FragmentService;

use Sonata\ArticleBundle\Model\FragmentInterface;

/**
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
interface ExtraContentProviderInterface
{
    /**
     * Gets extra content (1 or multi dimensional array) to complete base fragment content.
     *
     * @param FragmentInterface $fragment
     *
     * @return array
     */
    public function getExtraContent(FragmentInterface $fragment);
}
