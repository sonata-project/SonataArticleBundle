<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Tests\Helper;

use Sonata\ArticleBundle\Helper\FragmentHelper;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Sonata\ArticleBundle\Helper\FragmentHelper
     */
    protected $fragmentHelper;

    /**
     * @var EngineInterface
     */
    protected $templating;

    protected function setUp()
    {
        $this->templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('render', 'exists', 'supports'))
            ->getMock();

        $this->fragmentHelper = new FragmentHelper($this->templating);
    }

    public function testRenderWithoutService()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Cannot render Fragment of type `foo.bar`. Service not found.');

        // templating render should not be called
        $this->templating->expects($this->never())->method('render');

        $fragment = $this->getFragmentMock();
        $this->fragmentHelper->render($fragment);
    }

    public function testRender()
    {
        $fragment = $this->getFragmentMock();

        // templating render must be called once
        $this->templating->expects($this->once())->method('render')->will($this->returnValue('foo'));

        $fragmentService = $this->createMock('Sonata\ArticleBundle\FragmentService\FragmentServiceInterface');
        $fragmentService->expects($this->once())->method('getTemplate')->will($this->returnValue('template.html.twig'));

        $this->fragmentHelper->setFragmentServices(array('foo.bar' => $fragmentService));
        $this->fragmentHelper->render($fragment);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFragmentMock()
    {
        $fragment = $this->createMock('Sonata\ArticleBundle\Model\FragmentInterface');
        $fragment->expects($this->once())->method('getType')->will($this->returnValue('foo.bar'));
        $fragment->expects($this->any())->method('getSettings')->will($this->returnValue(array()));

        return $fragment;
    }
}
