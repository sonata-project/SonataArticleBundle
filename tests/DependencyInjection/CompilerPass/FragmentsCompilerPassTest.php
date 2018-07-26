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

namespace Sonata\ArticleBundle\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\DependencyInjection\CompilerPass\FragmentsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class FragmentsCompilerPassTest extends TestCase
{
    public function testProcessWithMissFormattedFragmentServices(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You need to specify the `key` argument to your tag.');

        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())->method('findTaggedServiceIds')
            ->with('sonata.article.fragment')
            ->willReturn(['foo']);

        $compilerPass = new FragmentsCompilerPass();
        $compilerPass->process($container);
    }

    public function testProcess(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('getParameter')
            ->with('sonata.article.admin.fragments.services')
            ->willReturn(['simple_array_provider' => []]);
        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('sonata.article.fragment')
            ->willReturn(['foo' => [['key' => 'bar']]]);
        $container->expects($this->exactly(2))->method('hasDefinition')
            ->withConsecutive(
                ['sonata.article.admin.fragment'], ['sonata.article.helper.fragment']
            )
            ->willReturn(true);

        $definition = $this->createMock(Definition::class);
        $definition->expects($this->exactly(2))->method('addMethodCall')->with('setFragmentServices', [[]]);
        $container->expects($this->any())->method('getDefinition')->willReturn($definition);

        $compilerPass = new FragmentsCompilerPass();
        $compilerPass->process($container);
    }
}
