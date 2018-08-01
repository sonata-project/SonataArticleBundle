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

namespace Sonata\ArticleBundle\Tests\Admin;

use Sonata\AdminBundle\Admin\AdminInterface as BaseAdminInterface;

/**
 * Add support for setBaseCodeRoute method (3.x).
 * Add support for addParentAssociationMapping method (4.x).
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
interface AdminInterface extends BaseAdminInterface
{
    public function setBaseCodeRoute(): void;

    public function addParentAssociationMapping(): void;
}
