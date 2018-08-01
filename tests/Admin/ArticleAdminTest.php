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

namespace Sonata\ArticleBundle\Tests\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\ArticleBundle\Admin\ArticleAdmin;
use Sonata\ArticleBundle\Entity\AbstractArticle;
use Sonata\ArticleBundle\Entity\AbstractFragment;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
class ArticleAdminTest extends AdminTestCase
{
    use AdminFormFieldTestTrait;

    /**
     * @var ArticleAdmin
     */
    protected $admin;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->admin = new ArticleAdmin($this->dummyAdminId, AbstractArticle::class, $this->dummyController, AbstractFragment::class);
        $this->mockDefaultServices($this->admin);
    }

    public function testToString(): void
    {
        $shortTitle = new class() {
            public function getTitle()
            {
                return 'A short title';
            }
        };
        $longTitle = new class() {
            public function getTitle()
            {
                return 'A very long title that describes Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt';
            }
        };

        $this->assertEquals('A short title', $this->admin->toString($shortTitle));
        $this->assertEquals(
            'A very long title that describes Lorem ipsum dolor sit amet, consectetur adipiscing...',
            $this->admin->toString($longTitle)
        );
    }

    public function testGetTemplate(): void
    {
        $this->assertEquals('@SonataArticle/FragmentAdmin/edit_article.html.twig', $this->admin->getTemplate('edit'));
        $this->assertNull($this->admin->getTemplate('foo'));
    }

    /**
     * @group legacy
     * @expectedDeprecation The Sonata\AdminBundle\Admin\AdminInterface::validate method is deprecated (this feature cannot be stable, use a custom validator, the feature will be removed with Symfony 2.2).
     */
    public function testValidate(): void
    {
        $errorElement = $this->getMockBuilder(ErrorElement::class)
            ->disableOriginalConstructor()
            ->setMethods(['with', 'end', 'assertNotNull', 'addViolation', 'validate'])
            ->getMock();
        $errorElement->expects($this->any())->method('with')->willReturnSelf();
        $errorElement->expects($this->any())->method('assertNotNull')->willReturnSelf();
        $errorElement->expects($this->any())->method('end')->willReturnSelf();
        $errorElement->expects($this->any())->method('addViolation')->willReturnSelf();
        $object = new class() {
            public function getFragments()
            {
                return [
                    new \stdClass(),
                    new \stdClass(),
                ];
            }
        };

        $fragmentAdmin = $this->createMock(AdminInterface::class);
        $fragmentAdmin->expects($this->any())->method('getCode')->willReturn('sonata.article.admin.fragment');
        $this->admin->addChild($fragmentAdmin, null);

        // Test that we validate the title and the status of articles
        $errorElement->expects($this->at(0))->method('with')->with('title');
        $errorElement->expects($this->at(3))->method('with')->with('status');
        // Test that each fragment linked to the article will be validated by the admin
        // tagged sonata.article.admin.fragment
        $fragmentAdmin->expects($this->exactly(2))->method('validate');

        $this->admin->validate($errorElement, $object);
    }

    public function testPrePersist(): void
    {
        $fragmentsRef = new ArrayCollection();
        $object = $this->createMock(ArticleInterface::class);
        $object->expects($this->any())->method('setFragments')->willReturnCallback(function ($fragments) use ($fragmentsRef): void {
            $fragmentsRef = $fragments;
        });
        $object->expects($this->any())->method('getFragments')->willReturnCallback(function () use ($fragmentsRef) {
            return $fragmentsRef;
        });

        $frag1 = $this->createMock(FragmentInterface::class);
        $frag2 = $this->createMock(FragmentInterface::class);
        $frag3 = $this->createMock(FragmentInterface::class);
        $frag1->expects($this->any())->method('getPosition')->willReturn(1);
        $frag2->expects($this->any())->method('getPosition')->willReturn(2);
        $frag3->expects($this->any())->method('getPosition')->willReturn(3);

        // Add the fragments in the wrong order
        $fragmentsRef->add($frag2);
        $fragmentsRef->add($frag3);
        $fragmentsRef->add($frag1);

        $fragmentAdmin = $this->createMock(AdminInterface::class);
        $fragmentAdmin->expects($this->any())->method('getCode')->willReturn('sonata.article.admin.fragment');
        $this->admin->addChild($fragmentAdmin, '');

        // Test that each fragment linked to the article will be prePersisted by the admin
        // tagged sonata.article.admin.fragment
        $fragmentAdmin->expects($this->exactly(3))->method('prePersist');

        $this->admin->prePersist($object);

        // Test that fragments are now sorted
        $this->assertEquals($frag1, $fragmentsRef->get(0));
        $this->assertEquals($frag2, $fragmentsRef->get(1));
        $this->assertEquals($frag3, $fragmentsRef->get(2));
    }

    public function testConfigureFormFields(): void
    {
        $formMapper = $this->mockFormMapper($this->admin);
        $this->expectInOrder($formMapper, [
           ['title', TextType::class],
           ['subtitle', TextType::class],
           ['abstract', TextareaType::class],
           ['status', ChoiceType::class],
           ['publicationStartsAt', DateTimePickerType::class],
           ['publicationEndsAt', DateTimePickerType::class],
           ['tags', ModelAutocompleteType::class],
           ['categories', ModelAutocompleteType::class],
           ['fragments', CollectionType::class],
        ]);
    }

    public function testConfigureListFields(): void
    {
        $listMapper = $this->createMock(ListMapper::class);
        $listMapper->expects($this->at(0))->method('addIdentifier')->with('title')->willReturnSelf();
        $listMapper->expects($this->at(1))->method('add')->with('updatedAt')->willReturnSelf();
        $listMapper->expects($this->at(2))->method('add')->with('status');

        $this->invokeMethod($this->admin, 'configureListFields', [$listMapper]);
    }

    public function testConfigureShowFields(): void
    {
        $showMapper = $this->createMock(ShowMapper::class);
        $showMapper->expects($this->at(0))->method('add')->with('title')->willReturnSelf();
        $showMapper->expects($this->at(1))->method('add')->with('subtitle')->willReturnSelf();
        $showMapper->expects($this->at(2))->method('add')->with('abstract');

        $this->invokeMethod($this->admin, 'configureShowFields', [$showMapper]);
    }
}
