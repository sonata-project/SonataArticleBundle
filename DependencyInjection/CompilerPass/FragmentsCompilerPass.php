<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class FragmentsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $fragmentServiceIds = $container->findTaggedServiceIds('sonata.article.fragment');
        $fragmentServices = array();
        $requiredFragmentsServices = $container->getParameter('sonata.article.admin.fragments.services');

        foreach ($fragmentServiceIds as $id => $attributes) {
            if (!isset($attributes[0]) || !isset($attributes[0]['key'])) {
                throw new \RuntimeException('You need to specify the `key` argument to your tag.');
            }

            if (in_array($id, $requiredFragmentsServices['simple_array_provider'])) {
                $fragmentServices[$attributes[0]['key']] = new Reference($id);
            }
        }

        if ($container->hasDefinition('sonata.article.admin.fragment')) {
            $fragmentAdminDef = $container->getDefinition('sonata.article.admin.fragment');
            $fragmentAdminDef->addMethodCall('setFragmentServices', array($fragmentServices));
        }

        if ($container->hasDefinition('sonata.article.helper.fragment')) {
            $fragmentAdminDef = $container->getDefinition('sonata.article.helper.fragment');
            $fragmentAdminDef->addMethodCall('setFragmentServices', array($fragmentServices));
        }

        if ($container->hasDefinition('sonata.article.fragment.validator')) {
            $fragmentValidDef = $container->getDefinition('sonata.article.fragment.validator');
            $fragmentValidDef->replaceArgument(0, $fragmentServices);
        }
    }
}
