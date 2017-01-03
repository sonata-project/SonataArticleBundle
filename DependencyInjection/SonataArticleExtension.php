<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
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
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('fragments.xml');

        if ($config['enable_fragments_rendering']) {
            $loader->load('helper.xml');
            $loader->load('twig.xml');
        }

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
        $this->configureClassesToCompile();
    }

    /**
     * Registers service parameters from bundle configuration.
     *
     * @param ContainerBuilder $container Container builder
     * @param array            $config    Array of configuration
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $modelNames = array(
            'article',
            'fragment',
            'category',
            'tag',
            'media',
        );

        foreach ($modelNames as $modelName) {
            $container->setParameter(sprintf('sonata.article.%s.class', $modelName), $config['class'][$modelName]);
            $container->setParameter(sprintf('sonata.article.admin.%s.entity', $modelName),
                $config['class'][$modelName]);
        }

        $container->setParameter('sonata.article.admin.fragments.services', $config['fragment_whitelist_provider']);

        $container->setParameter('sonata.article.admin.article.class', $config['admin']['article']);

        $container->setParameter('sonata.article.admin.article.controller', $config['admin_controller']['article']);
        $container->setParameter('sonata.article.admin.fragment.controller', $config['admin_controller']['fragment']);

        $container->setParameter('sonata.article.admin.article.translation_domain', $config['translation_domain']);

        $container->setParameter('sonata.article.admin.article.max_length_title_for_display', $config['max_length_title_for_display']);
    }

    /**
     * Registers doctrine mapping on concrete page entities.
     *
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['article']) || !class_exists($config['class']['fragment'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['article'], 'mapOneToMany', array(
            'fieldName' => 'fragments',
            'targetEntity' => $config['class']['fragment'],
            'cascade' => array(
                'remove',
                'persist',
            ),
            'mappedBy' => 'article',
            'orphanRemoval' => true,
            'orderBy' => array(
                'position' => 'ASC',
            ),
        ));

        if (class_exists($config['class']['category'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToMany', array(
                'fieldName' => 'categories',
                'targetEntity' => $config['class']['category'],
                'cascade' => array(),
                'joinTable' => array(
                    'name' => 'article__article_categories',
                    'joinColumns' => array(
                        array(
                            'name' => 'article_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ),
                    ),
                    'inverseJoinColumns' => array(
                        array(
                            'name' => 'category_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ),
                    ),
                ),
            ));
        }

        if (class_exists($config['class']['tag'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToMany', array(
                'fieldName' => 'tags',
                'targetEntity' => $config['class']['tag'],
                'cascade' => array(),
                'joinTable' => array(
                    'name' => 'article__article_tags',
                    'joinColumns' => array(
                        array(
                            'name' => 'article_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ),
                    ),
                    'inverseJoinColumns' => array(
                        array(
                            'name' => 'tag_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                        ),
                    ),
                ),
            ));
        }

        if (class_exists($config['class']['media'])) {
            $collector->addAssociation($config['class']['article'], 'mapManyToOne', array(
                'fieldName' => 'mainImage',
                'targetEntity' => $config['class']['media'],
                'cascade' => array(
                    'persist',
                ),
                'mappedBy' => null,
                'joinColumns' => array(
                    array(
                        'name' => 'main_image_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'orphanRemoval' => false,
            ));
        }

        $collector->addAssociation($config['class']['fragment'], 'mapManyToOne', array(
            'fieldName' => 'article',
            'targetEntity' => $config['class']['article'],
            'cascade' => array(
                'persist',
            ),
            'mappedBy' => null,
            'inversedBy' => 'fragments',
            'joinColumns' => array(
                array(
                    'name' => 'article_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }

    /**
     * Add class to compile.
     */
    public function configureClassesToCompile()
    {
        $this->addClassesToCompile(array(
            'Sonata\\ArticleBundle\\Entity\\AbstractArticle',
            'Sonata\\ArticleBundle\\Entity\\AbstractFragment',
            'Sonata\\ArticleBundle\\Model\\AbstractArticle',
            'Sonata\\ArticleBundle\\Model\\ArticleFragmentInterface',
            'Sonata\\ArticleBundle\\Model\\ArticleInterface',
            'Sonata\\ArticleBundle\\Model\\AbstractFragment',
            'Sonata\\ArticleBundle\\Model\\FragmentInterface',
            'Sonata\\ArticleBundle\\FragmentService\\FragmentServiceInterface',
            'Sonata\\ArticleBundle\\FragmentService\\AbstractFragmentService',
            'Sonata\\ArticleBundle\\FragmentService\\TitleFragmentService',
            'Sonata\\ArticleBundle\\FragmentService\\TextFragmentService',
            'Sonata\\ArticleBundle\\FragmentService\\Validator\\FragmentConstraint',
            'Sonata\\ArticleBundle\\FragmentService\\Validator\\FragmentValidator',
        ));
    }
}
