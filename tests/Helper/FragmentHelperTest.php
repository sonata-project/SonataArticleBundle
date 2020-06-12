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

namespace Sonata\ArticleBundle\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\ArticleBundle\FragmentService\ExtraContentProviderInterface;
use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\ArticleBundle\Helper\FragmentHelper;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentHelperTest extends TestCase
{
    /**
     * @var FragmentHelper
     */
    protected $fragmentHelper;

    /**
     * @var EngineInterface
     */
    protected $templating;

    protected function setUp(): void
    {
        $this->templating = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['render', 'exists', 'supports'])
            ->getMock();

        $this->fragmentHelper = new FragmentHelper($this->templating);
    }

    public function testRenderWithoutService(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot render Fragment of type `foo.bar`. Service not found.');

        // templating render should not be called
        $this->templating->expects($this->never())->method('render');

        $fragment = $this->getFragmentMock();
        $this->fragmentHelper->render($fragment);
    }

    public function testRender(): void
    {
        $fragment = $this->getFragmentMock();

        // templating render must be called once
        $this->templating->expects($this->once())->method('render')->willReturn('foo');

        $fragmentService = $this->createMock(MockFragmentServiceInterface::class);

        $fragmentService->expects($this->once())->method('getTemplate')->willReturn('template.html.twig');
        $fragmentService->expects($this->once())->method('getExtraContent')->willReturn(['foo' => 'bar']);

        $this->fragmentHelper->setFragmentServices(['foo.bar' => $fragmentService]);
        $this->fragmentHelper->render($fragment);

        $this->assertArrayHasKey('foo.bar', $this->fragmentHelper->getFragmentServices());
        $this->assertInstanceOf(
            FragmentServiceInterface::class,
            $this->fragmentHelper->getFragmentServices()['foo.bar']
        );
    }

    private function getFragmentMock(): MockObject
    {
        $fragment = $this->createMock(FragmentInterface::class);
        $fragment->expects($this->once())->method('getType')->willReturn('foo.bar');
        $fragment->expects($this->any())->method('getFields')->willReturn([]);

        return $fragment;
    }
}

interface MockFragmentServiceInterface extends FragmentServiceInterface, ExtraContentProviderInterface
{
}
