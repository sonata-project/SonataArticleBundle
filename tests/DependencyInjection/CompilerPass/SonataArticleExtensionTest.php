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

namespace Sonata\ArticleBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\ArticleBundle\DependencyInjection\SonataArticleExtension;

final class SonataArticleExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sonata.article.admin.article', 'Sonata\ArticleBundle\Admin\ArticleAdmin');
        $this->assertContainerBuilderHasService('sonata.article.admin.fragment', 'Sonata\ArticleBundle\Admin\FragmentAdmin');
        $this->assertContainerBuilderHasService('sonata.article.fragment.validator', 'Sonata\ArticleBundle\FragmentService\Validator\FragmentValidator');
        $this->assertContainerBuilderHasService('sonata.article.fragment.title', 'Sonata\ArticleBundle\FragmentService\TitleFragmentService');
        $this->assertContainerBuilderHasService('sonata.article.fragment.text', 'Sonata\ArticleBundle\FragmentService\TextFragmentService');

        $this->assertContainerBuilderHasParameter('sonata.article.article.class', 'Application\Sonata\ArticleBundle\Entity\Article');
        $this->assertContainerBuilderHasParameter('sonata.article.fragment.class', 'Application\Sonata\ArticleBundle\Entity\Fragment');
        $this->assertContainerBuilderHasParameter('sonata.article.category.class', 'Application\Sonata\ClassificationBundle\Entity\Category');
        $this->assertContainerBuilderHasParameter('sonata.article.tag.class', 'Application\Sonata\ClassificationBundle\Entity\Tag');
        $this->assertContainerBuilderHasParameter('sonata.article.media.class', 'Application\Sonata\MediaBundle\Entity\Media');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.article.entity', 'Application\Sonata\ArticleBundle\Entity\Article');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.fragment.entity', 'Application\Sonata\ArticleBundle\Entity\Fragment');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.category.entity', 'Application\Sonata\ClassificationBundle\Entity\Category');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.tag.entity', 'Application\Sonata\ClassificationBundle\Entity\Tag');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.media.entity', 'Application\Sonata\MediaBundle\Entity\Media');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.fragments.services', ['simple_array_provider' => []]);
        $this->assertContainerBuilderHasParameter('sonata.article.admin.article.class', 'Sonata\ArticleBundle\Admin\ArticleAdmin');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.article.controller', 'SonataAdminBundle:CRUD');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.fragment.controller', 'SonataArticleBundle:FragmentAdmin');
        $this->assertContainerBuilderHasParameter('sonata.article.admin.article.translation_domain', 'SonataArticleBundle');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SonataArticleExtension(),
        ];
    }
}
