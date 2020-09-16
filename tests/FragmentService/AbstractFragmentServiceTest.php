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

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ArticleBundle\FragmentService\AbstractFragmentService;
use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\ArticleBundle\Model\AbstractFragment;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractFragmentServiceTest extends TestCase
{
    public function testFragmentService(): void
    {
        $fragmentService = $this->getFragmentService();

        $this->assertSame('fragmentService', $fragmentService->getName());
        $this->assertSame('@SonataArticle/FragmentAdmin/form.html.twig', $fragmentService->getEditTemplate());
        $this->assertInstanceOf(FragmentServiceInterface::class, $fragmentService);
    }

    public function testValidateBackofficeTitleNotEmpty(): void
    {
        $fragmentService = $this->getFragmentService();

        $fragment = $this->createMock(AbstractFragment::class);
        $fragment
            ->method('getBackofficeTitle')
            ->willReturn('');

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $errorElement = $this->createErrorElement($executionContext);
        $executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Fragment fragmentService - `Backoffice Title` must not be empty')
            ->willReturn($this->createConstraintBuilder());

        $fragmentService->validate($errorElement, $fragment);
    }

    public function testBuildEditForm(): void
    {
        $this->assertBuildForm('buildEditForm');
    }

    public function testBuildCreateForm(): void
    {
        $this->assertBuildForm('buildCreateForm');
    }

    private function assertBuildForm(string $method): void
    {
        $fragmentService = $this->getFragmentService();

        $formMapper = $this->createMock(FormMapper::class);
        $formMapper->expects($this->once())
            ->method('add')
            ->with('backofficeTitle');

        $fragmentService->$method($formMapper, $this->createMock(FragmentInterface::class));
    }

    private function getFragmentService(): FragmentService
    {
        return new FragmentService('fragmentService');
    }

    private function createErrorElement(ExecutionContextInterface $executionContext): ErrorElement
    {
        return new ErrorElement(
            '',
            $this->createStub(ConstraintValidatorFactoryInterface::class),
            $executionContext,
            'group'
        );
    }

    /**
     * @return Stub&ConstraintViolationBuilderInterface
     */
    private function createConstraintBuilder(): object
    {
        $constraintBuilder = $this->createStub(ConstraintViolationBuilderInterface::class);
        $constraintBuilder
            ->method('atPath')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setParameters')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setTranslationDomain')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setInvalidValue')
            ->willReturn($constraintBuilder);

        return $constraintBuilder;
    }
}

class FragmentService extends AbstractFragmentService
{
    public function getTemplate(): string
    {
    }
}
