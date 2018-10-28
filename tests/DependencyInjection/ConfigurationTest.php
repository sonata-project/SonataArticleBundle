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

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            [],
        ], [
            'class' => [
                'article' => 'Application\Sonata\ArticleBundle\Entity\Article',
                'fragment' => 'Application\Sonata\ArticleBundle\Entity\Fragment',
                'category' => 'Application\Sonata\ClassificationBundle\Entity\Category',
                'tag' => 'Application\Sonata\ClassificationBundle\Entity\Tag',
                'media' => 'Application\Sonata\MediaBundle\Entity\Media',
            ],
            'admin' => [
                'article' => 'Sonata\ArticleBundle\Admin\ArticleAdmin',
            ],
            'admin_controller' => [
                'article' => 'SonataAdminBundle:CRUD',
                'fragment' => 'SonataArticleBundle:FragmentAdmin',
            ],
             'translation_domain' => 'SonataArticleBundle',
             'enable_fragments_rendering' => true,
             'max_length_title_for_display' => 80,
             'fragment_whitelist_provider' => ['simple_array_provider' => []],
        ]);
    }
}
