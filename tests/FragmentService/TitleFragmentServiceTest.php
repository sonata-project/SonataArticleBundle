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

namespace Sonata\ArticleBundle\Tests\FragmentService;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ArticleBundle\FragmentService\AbstractFragmentService;
use Sonata\ArticleBundle\FragmentService\TitleFragmentService;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class TitleFragmentServiceTest extends TestCase
{
    public function testFragmentService(): void
    {
        $fragmentService = $this->getFragmentService();

        $this->assertSame('@SonataArticle/Fragment/fragment_title.html.twig', $fragmentService->getTemplate());
        $this->assertInstanceOf(AbstractFragmentService::class, $fragmentService);
    }

    public function testValidateTextNotEmpty(): void
    {
        $fragmentService = $this->getFragmentService();

        $fragment = $this->createMock(FragmentInterface::class);
        $fragment->expects($this->any())
            ->method('getField')
            ->with('text')
            ->willReturn('');

        $errorElement = $this->createMock(ErrorElement::class);
        $errorElement->expects($this->once())
            ->method('addViolation')
            ->with('Fragment Title - `Text` must not be empty');

        $fragmentService->validate($errorElement, $fragment);
    }

    public function testValidateTextMaxLength(): void
    {
        $fragmentService = $this->getFragmentService();

        $fragment = $this->createMock(FragmentInterface::class);
        $fragment->expects($this->any())
            ->method('getField')
            ->with('text')
            ->willReturn('A very long text over 255 characters. A very long text over 255 characters. A very long text over 255 characters. A very long text over 255 characters. A very long text over 255 characters. A very long text over 255 characters. A very long text over 255 characters.');

        $errorElement = $this->createMock(ErrorElement::class);
        $errorElement->expects($this->once())
            ->method('addViolation')
            ->with('Fragment Text - `Text` must not be longer than 255 characters.');

        $fragmentService->validate($errorElement, $fragment);
    }

    public function testBuildForm(): void
    {
        $fragmentService = $this->getFragmentService();

        $formMapper = $this->createMock(FormMapper::class);
        $formMapper->expects($this->once())
            ->method('add')
            ->with(
                'fields',
                ImmutableArrayType::class,
                $this->callback(function ($settingsConfig) {
                    $this->assertCount(1, array_keys($settingsConfig['keys']));

                    $fieldConfig = $settingsConfig['keys'][0];
                    $this->assertSame('text', $fieldConfig[0]);
                    $this->assertSame(TextType::class, $fieldConfig[1]);

                    $fieldOptions = $fieldConfig[2];
                    $this->assertSame('Title', $fieldOptions['label']);
                    $this->assertCount(2, $fieldOptions['constraints']);
                    $this->assertInstanceOf(NotBlank::class, $fieldOptions['constraints'][0]);
                    $this->assertInstanceOf(Length::class, $fieldOptions['constraints'][1]);

                    return true;
                })
            );

        $fragmentService->buildForm($formMapper, $this->createMock(FragmentInterface::class));
    }

    private function getFragmentService(): TitleFragmentService
    {
        return new TitleFragmentService('title');
    }
}
