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
use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\ArticleBundle\Model\AbstractFragment;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\CoreBundle\Validator\ErrorElement;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class AbstractFragmentServiceTest extends TestCase
{
    public function testFragmentService(): void
    {
        $fragmentService = $this->getFragmentService();

        $this->assertEquals('fragmentService', $fragmentService->getName());
        $this->assertEquals('@SonataArticle/FragmentAdmin/form.html.twig', $fragmentService->getEditTemplate());
        $this->assertInstanceOf(FragmentServiceInterface::class, $fragmentService);
    }

    public function testValidateBackofficeTitleNotEmpty(): void
    {
        $fragmentService = $this->getFragmentService();

        $fragment = $this->createMock(AbstractFragment::class);
        $fragment->expects($this->any())
            ->method('getBackofficeTitle')
            ->willReturn('');

        $errorElement = $this->createMock(ErrorElement::class);
        $errorElement->expects($this->once())
            ->method('addViolation')
            ->with('Fragment fragmentService - `Backoffice Title` must not be empty');

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

    private function assertBuildForm($method): void
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
}

class FragmentService extends AbstractFragmentService
{
    public function getTemplate(): void
    {
    }
}
