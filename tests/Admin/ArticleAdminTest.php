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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\ArticleBundle\Admin\ArticleAdmin;
use Sonata\ArticleBundle\Entity\AbstractArticle;

class ArticleAdminTest extends TestCase
{
    public function testToString(): void
    {
        $articleAdmin = new ArticleAdmin(
            'admin.article',
            AbstractArticle::class,
            CRUDController::class,
            AbstractFragment::class,
            20
        );
        /** @var AbstractArticle|MockObject $article */
        $article = $this->getMockForAbstractClass(AbstractArticle::class);

        $article->setTitle('short title');
        $this->assertSame('short title', $articleAdmin->toString($article));

        $article->setTitle('longer than 20 chars with breakpoint');
        $this->assertSame('longer than 20 chars...', $articleAdmin->toString($article));

        $article->setTitle('longer than 20 charnobreakpoint');
        $this->assertSame('longer than 20 charnobreakpoint', $articleAdmin->toString($article));
    }
}
