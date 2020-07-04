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

namespace Sonata\ArticleBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\ArticleBundle\Model\AbstractArticle;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DateTimePickerType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Florent Denis <florent.denis@ekino.com>
 */
class ArticleAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $datagridValues = ['_sort_by' => 'updatedAt', '_sort_order' => 'DESC'];

    /**
     * @var string
     */
    protected $fragmentClass;

    /**
     * @var int
     */
    protected $maxLengthTitleForDisplay;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        string $fragmentClass,
        int $maxLengthTitleForDisplay
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->fragmentClass = $fragmentClass;
        $this->maxLengthTitleForDisplay = $maxLengthTitleForDisplay;
    }

    public function toString($object): string
    {
        $value = $object->getTitle();
        $length = $this->maxLengthTitleForDisplay;
        $separator = '...';

        if (mb_strlen($value) > $length) {
            // If breakpoint is on the last word, return the value without separator.
            if (false === ($breakpoint = mb_strpos($value, ' ', $length))) {
                return $value;
            }

            $length = $breakpoint;

            return rtrim(mb_substr($value, 0, $length)).$separator;
        }

        return $value;
    }

    public function getTemplate($name): ?string
    {
        if ('edit' === $name) {
            return '@SonataArticle/FragmentAdmin/edit_article.html.twig';
        }

        return parent::getTemplate($name);
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $errorElement
            ->with('title')
                ->assertNotNull()
            ->end()
            ->with('status')
                ->assertChoice(array_keys($this->isGranted('ROLE_ARTICLE_PUBLISH') ?
                    AbstractArticle::getStatuses() : AbstractArticle::getContributorStatus()))
            ->end()
        ;

        $fragmentAdmin = $this->getChild('sonata.article.admin.fragment');
        foreach ($object->getFragments() as $fragment) {
            $fragmentAdmin->validate($errorElement, $fragment);
        }
    }

    /**
     * @param ArticleInterface $object
     */
    public function prePersist($object): void
    {
        $this->sort($object);

        $fragmentAdmin = $this->getChild('sonata.article.admin.fragment');
        foreach ($object->getFragments() as $fragment) {
            $fragmentAdmin->prePersist($fragment);
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $fragmentClass = $this->fragmentClass;
        $subject = $this->getSubject();

        $formMapper
            ->with('General', ['class' => 'col-md-8'])
                ->add('title', TextType::class, [
                    'attr' => ['maxlength' => 255],
                    'constraints' => [new NotBlank()],
                    'empty_data' => '',
                ])
                ->add('subtitle', TextType::class, [
                    'required' => false,
                    'attr' => ['maxlength' => 255],
                ])
                ->add('abstract', TextareaType::class, [
                    'required' => false,
                ])
            ->end()

            ->with('Publication', ['class' => 'col-md-4'])
                ->add('status', ChoiceType::class, [
                    'choices' => array_flip($this->isGranted('ROLE_ARTICLE_PUBLISH') ?
                        AbstractArticle::getStatuses() : AbstractArticle::getContributorStatus()),
                    'attr' => ['class' => 'full-width'],
                ])
                ->add('publicationStartsAt', DateTimePickerType::class, [
                    'format' => 'dd/MM/yyyy HH:mm',
                    'datepicker_use_button' => false,
                    'dp_side_by_side' => true,
                    'dp_language' => 'fr',
                    'required' => false,
                ])
                ->add('publicationEndsAt', DateTimePickerType::class, [
                    'format' => 'dd/MM/yyyy HH:mm',
                    'datepicker_use_button' => false,
                    'dp_side_by_side' => true,
                    'dp_language' => 'fr',
                    'required' => false,
                ])
            ->end()

            ->with('Tags', ['class' => 'col-md-6'])
                ->add('tags', ModelAutocompleteType::class, [
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'label' => false,
                    'attr' => ['class' => 'show'],
                    'callback' => static function (AdminInterface $admin, $property, $searchText): void {
                        $datagrid = $admin->getDatagrid();
                        $datagrid->setValue($property, null, $searchText);
                        $datagrid->setValue('enabled', null, true);
                    },
                ])
            ->end()

            ->with('Categories', ['class' => 'col-md-6'])
                ->add('categories', ModelAutocompleteType::class, [
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'label' => false,
                    'attr' => ['class' => 'show'],
                    'callback' => static function (AdminInterface $admin, $property, $searchText): void {
                        $datagrid = $admin->getDatagrid();
                        $datagrid->setValue($property, null, $searchText);
                        $datagrid->setValue('enabled', null, true);
                    },
                ])
            ->end()

            ->with('Fragments', ['class' => 'col-md-12'])
                ->add('fragments', CollectionType::class, [
                    'constraints' => [new Valid()],
                    'by_reference' => false,
                    'label' => false,
                    // callback of mapping fragment with the one selected on the list
                    'pre_bind_data_callback' => static function ($value) use ($fragmentClass, $subject) {
                        $fragment = null;
                        // existing fragment case
                        foreach ($subject->getFragments() as $existingFragment) {
                            if (null !== $existingFragment->getId() && $existingFragment->getId() === $value['id']) {
                                $fragment = $existingFragment;

                                break;
                            }
                        }
                        // new fragment case
                        if (!$fragment) {
                            /** @var FragmentInterface $fragment */
                            $fragment = new $fragmentClass();
                            $fragment->setType($value['type']);
                            $fragment->setEnabled(isset($value['enabled']) ? (bool) $value['enabled'] : false);
                            $fragment->setPosition($value['position'] ?: 1);
                            $fragment->setFields((isset($value['fields']) && \is_array($value['fields'])) ? $value['fields'] : []);

                            $subject->addFragment($fragment);
                        }

                        return $fragment;
                    },
                ], [
                    'sortable' => 'position',
                ])
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title', 'text', [
                'sortable' => true,
            ])
            ->add('updatedAt', 'datetime', [
                'sortable' => true,
            ])
            ->add('status', 'choice', [
                'sortable' => true,
                'choices' => AbstractArticle::getStatuses(),
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title', 'text')
            ->add('subtitle', 'text')
            ->add('abstract', 'textarea')
        ;
    }

    /**
     * Sorting the fragments depending on position.
     */
    private function sort(ArticleInterface $article): void
    {
        $fragments = $article->getFragments()->toArray();

        usort($fragments, static function (FragmentInterface $a, FragmentInterface $b) {
            return $a->getPosition() - $b->getPosition();
        });

        $article->setFragments(new ArrayCollection($fragments));
    }
}
