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
use Sonata\ArticleBundle\FragmentService\TextFragmentService;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class TextFragmentServiceTest extends TestCase
{
    public function testFragmentService(): void
    {
        $fragmentService = $this->getFragmentService();

        $this->assertSame('@SonataArticle/Fragment/fragment_text.html.twig', $fragmentService->getTemplate());
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
            ->with('Fragment Text - `Text` must not be empty');

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
                    $this->assertSame(TextareaType::class, $fieldConfig[1]);

                    $fieldOptions = $fieldConfig[2];
                    $this->assertSame('Text', $fieldOptions['label']);
                    $this->assertCount(1, $fieldOptions['constraints']);
                    $this->assertInstanceOf(NotBlank::class, $fieldOptions['constraints'][0]);

                    return true;
                })
            );

        $fragmentService->buildForm($formMapper, $this->createMock(FragmentInterface::class));
    }

    private function getFragmentService(): TextFragmentService
    {
        return new TextFragmentService('text');
    }
}
