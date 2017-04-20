<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Tests\Twig;

use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\ArticleBundle\Twig\FragmentExtension;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Sonata\ArticleBundle\Helper\FragmentHelper
     */
    protected $fragmentHelper;

    /**
     * @var \Sonata\ArticleBundle\Twig\FragmentExtension
     */
    protected $fragmentExtension;

    protected function setUp()
    {
        $this->fragmentHelper = $this->getMockBuilder('Sonata\ArticleBundle\Helper\FragmentHelper')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();

        $this->fragmentExtension = new FragmentExtension($this->fragmentHelper);
    }

    public function testRenderFragment()
    {
        // We render one fragment
        $fragment = $this->getFragmentMock(
            array(
                'title' => 'foo',
                'body' => 'bar',
            )
        );

        $this->fragmentHelper->expects($this->once())
            ->method('render')
            ->willReturnCallback(array($this, 'renderFragment'));

        $this->assertEquals(
            '<h1>foo</h1><p>bar</p>',
            $this->fragmentExtension->renderFragment($fragment)
        );
    }

    public function testRenderArticleFragment()
    {
        $fragments = array();

        // We render 3 fragments with one disabled
        for ($i = 0; $i < 3; ++$i) {
            $fragments[] = $this->getFragmentMock(
                array(
                    'title' => 'foo'.$i,
                    'body' => 'bar'.$i,
                ),
                !($i % 2)
            );
        }

        $article = $this->createMock('Sonata\ArticleBundle\Model\ArticleInterface');
        $article->expects($this->any())
            ->method('getFragments')
            ->will($this->returnValue($fragments));

        // we expect only two calls
        $this->fragmentHelper->expects($this->at(0))
            ->method('render')
            ->willReturnCallback(array($this, 'renderFragment'));
        $this->fragmentHelper->expects($this->at(1))
            ->method('render')
            ->willReturnCallback(array($this, 'renderFragment'));

        $this->assertEquals(
            '<h1>foo0</h1><p>bar0</p><h1>foo2</h1><p>bar2</p>',
            $this->fragmentExtension->renderArticleFragments($article)
        );
    }

    public function renderFragment(FragmentInterface $fragment)
    {
        $fields = $fragment->getSettings();

        return sprintf('<h1>%s</h1><p>%s</p>', $fields['title'], $fields['body']);
    }

    /**
     * @param array $settings
     * @param bool  $enabled
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFragmentMock(array $settings, $enabled = true)
    {
        $fragment = $this->createMock('Sonata\ArticleBundle\Model\FragmentInterface');
        $fragment->expects($this->any())
            ->method('getSettings')
            ->will($this->returnValue($settings));
        $fragment->expects($this->any())
            ->method('getEnabled')
            ->will($this->returnValue($enabled));

        return $fragment;
    }
}
