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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Benoit Mazière <bmaziere@ekino.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_article')->children();

        $node
            ->arrayNode('class')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('article')
                        ->defaultValue('Application\\Sonata\\ArticleBundle\\Entity\\Article')
                    ->end()
                    ->scalarNode('fragment')
                        ->defaultValue('Application\\Sonata\\ArticleBundle\\Entity\\Fragment')
                    ->end()
                    ->scalarNode('category')
                        ->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Category')
                    ->end()
                    ->scalarNode('tag')
                        ->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Tag')
                    ->end()
                    ->scalarNode('media')
                        ->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')
                    ->end()
                ->end()
            ->end()

            ->arrayNode('admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('article')
                        ->defaultValue('Sonata\\ArticleBundle\\Admin\\ArticleAdmin')
                    ->end()
                ->end()
            ->end()

            ->arrayNode('fragment_whitelist_provider')
                ->children()
                    ->arrayNode('simple_array_provider')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()

            ->arrayNode('admin_controller')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('article')
                        ->defaultValue('SonataAdminBundle:CRUD')
                    ->end()
                    ->scalarNode('fragment')
                        ->defaultValue('SonataArticleBundle:FragmentAdmin')
                    ->end()
                ->end()
            ->end()

            ->scalarNode('translation_domain')
                ->defaultValue('SonataArticleBundle')
            ->end()

            ->scalarNode('enable_fragments_rendering')
                ->info('Enable/disable Twig Extension used for fragment rendering')
                ->defaultValue(true)
            ->end()

            ->scalarNode('max_length_title_for_display')
                ->defaultValue(80)
            ->end()
        ;

        return $treeBuilder;
    }
}
