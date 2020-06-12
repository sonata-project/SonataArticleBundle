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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Sonata\ArticleBundle\Admin\ArticleAdmin;
use Sonata\ArticleBundle\DependencyInjection\Configuration;
use Sonata\ArticleBundle\DependencyInjection\SonataArticleExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            'class' => [
                'article' => 'Application\Sonata\ArticleBundle\Entity\Article',
                'fragment' => 'Application\Sonata\ArticleBundle\Entity\Fragment',
                'category' => 'Application\Sonata\ClassificationBundle\Entity\Category',
                'tag' => 'Application\Sonata\ClassificationBundle\Entity\Tag',
                'media' => 'Application\Sonata\MediaBundle\Entity\Media',
            ],
            'admin' => [
                'article' => ArticleAdmin::class,
            ],
            'admin_controller' => [
                'article' => 'SonataAdminBundle:CRUD',
                'fragment' => 'SonataArticleBundle:FragmentAdmin',
            ],
            'translation_domain' => 'SonataArticleBundle',
            'enable_fragments_rendering' => true,
            'max_length_title_for_display' => 80,
            'fragment_whitelist_provider' => ['simple_array_provider' => []],
        ], [
            __DIR__.'/../Fixtures/configuration.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new SonataArticleExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
