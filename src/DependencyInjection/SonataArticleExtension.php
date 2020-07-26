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

namespace Sonata\ArticleBundle\DependencyInjection;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
final class SonataArticleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('fragments.xml');

        if ($config['enable_fragments_rendering']) {
            $loader->load('helper.xml');
            $loader->load('twig.xml');
        }

        $this->registerParameters($container, $config);

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
            $this->registerDoctrineMapping($config);
        }
    }

    /**
     * Registers service parameters from bundle configuration.
     *
     * @param ContainerBuilder $container Container builder
     * @param array            $config    Array of configuration
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $modelNames = [
            'article',
            'fragment',
            'category',
            'tag',
            'media',
        ];

        foreach ($modelNames as $modelName) {
            $container->setParameter(sprintf('sonata.article.%s.class', $modelName), $config['class'][$modelName]);
            $container->setParameter(
                sprintf('sonata.article.admin.%s.entity', $modelName),
                $config['class'][$modelName]
            );
        }

        $container->setParameter('sonata.article.admin.fragments.services', $config['fragment_whitelist_provider']);

        $container->setParameter('sonata.article.admin.article.class', $config['admin']['article']);

        $container->setParameter('sonata.article.admin.article.controller', $config['admin_controller']['article']);
        $container->setParameter('sonata.article.admin.fragment.controller', $config['admin_controller']['fragment']);

        $container->setParameter('sonata.article.admin.article.translation_domain', $config['translation_domain']);

        $container->setParameter('sonata.article.admin.article.max_length_title_for_display', $config['max_length_title_for_display']);
    }

    /**
     * NEXT MAJOR: Remove this method.
     *
     * Registers doctrine mapping on concrete page entities.
     */
    public function registerDoctrineMapping(array $config): void
    {
        @trigger_error(
            'Using SonataEasyExtendsBundle is deprecated since sonata-project/article-bundle 1.x. Please register SonataDoctrineBundle as a bundle instead.',
            E_USER_DEPRECATED
        );

        if (!class_exists($config['class']['article']) || !class_exists($config['class']['fragment'])) {
            return;
        }

        $collector = DeprecatedDoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['article'], 'mapOneToMany', [
            'fieldName' => 'fragments',
            'targetEntity' => $config['class']['fragment'],
            'cascade' => [
                'remove',
                'persist',
            ],
            'mappedBy' => 'article',
            'orphanRemoval' => true,
            'orderBy' => [
                'position' => 'ASC',
            ],
        ]);

        if (class_exists($config['class']['category'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToMany', [
                'fieldName' => 'categories',
                'targetEntity' => $config['class']['category'],
                'cascade' => [],
                'joinTable' => [
                    'name' => 'article__article_categories',
                    'joinColumns' => [
                        [
                            'name' => 'article_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ],
                    ],
                    'inverseJoinColumns' => [
                        [
                            'name' => 'category_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ],
                    ],
                ],
            ]);
        }

        if (class_exists($config['class']['tag'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToMany', [
                'fieldName' => 'tags',
                'targetEntity' => $config['class']['tag'],
                'cascade' => [],
                'joinTable' => [
                    'name' => 'article__article_tags',
                    'joinColumns' => [
                        [
                            'name' => 'article_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ],
                    ],
                    'inverseJoinColumns' => [
                        [
                            'name' => 'tag_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ],
                    ],
                ],
            ]);
        }

        if (class_exists($config['class']['media'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToOne', [
                'fieldName' => 'mainImage',
                'targetEntity' => $config['class']['media'],
                'cascade' => [
                    'persist',
                ],
                'mappedBy' => null,
                'joinColumns' => [
                    [
                        'name' => 'main_image_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
                'orphanRemoval' => false,
            ]);
        }

        $collector->addAssociation($config['class']['fragment'], 'mapManyToOne', [
            'fieldName' => 'article',
            'targetEntity' => $config['class']['article'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'fragments',
            'joinColumns' => [
                [
                    'name' => 'article_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);
    }

    private function registerSonataDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['article']) || !class_exists($config['class']['fragment'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['article'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('fragments', $config['class']['fragment'])
                ->cascade(['remove', 'persist'])
                ->mappedBy('article')
                ->orphanRemoval()
                ->addOrder('position', 'ASC')
        );

        $collector->addAssociation(
            $config['class']['fragment'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('article', $config['class']['article'])
                ->cascade(['persist'])
                ->inversedBy('fragments')
                ->addJoin([
                    'name' => 'article_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        if (class_exists($config['class']['category'])) {
            $collector->addAssociation(
                $config['class']['article'],
                'mapManyToMany',
                OptionsBuilder::createManyToMany('categories', $config['class']['category'])
                    ->addJoinTable('article__article_categories', [[
                        'name' => 'article_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ]], [[
                        'name' => 'category_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ]])
            );
        }

        if (class_exists($config['class']['tag'])) {
            $collector->addAssociation(
                $config['class']['article'],
                'mapManyToMany',
                OptionsBuilder::createManyToMany('tags', $config['class']['tag'])
                    ->addJoinTable('article__article_tags', [[
                        'name' => 'article_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ]], [[
                        'name' => 'tag_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ]])
            );
        }

        if (class_exists($config['class']['media'])) {
            $collector->addAssociation(
                $config['class']['article'],
                'mapManyToOne',
                OptionsBuilder::createManyToOne('mainImage', $config['class']['media'])
                    ->cascade(['persist'])
                    ->addJoin([
                        'name' => 'main_image_id',
                        'referencedColumnName' => 'id',
                    ])
            );
        }
    }
}
