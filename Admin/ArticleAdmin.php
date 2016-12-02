<?php

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
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\ArticleBundle\Model\AbstractArticle;
use Sonata\ArticleBundle\Model\ArticleInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Sonata\CoreBundle\Validator\ErrorElement;

/**
 * @author Florent Denis <florent.denis@ekino.com>
 */
class ArticleAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $datagridValues = array('_sort_by' => 'updatedAt', '_sort_order' => 'DESC');

    /**
     * @var string
     */
    protected $fragmentClass;

    /**
     * @var int
     */
    protected $maxLengthTitleForDisplay;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param string $fragmentClass
     * @param int    $maxLengthTitleForDisplay
     */
    public function __construct($code, $class, $baseControllerName, $fragmentClass, $maxLengthTitleForDisplay)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fragmentClass = $fragmentClass;
        $this->maxLengthTitleForDisplay = $maxLengthTitleForDisplay;
    }

    /**
     * Inspired from Twig_Extensions_Extension_Text.
     *
     * {@inheritdoc}
     */
    public function toString($object)
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

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        if ('edit' === $name) {
            return 'SonataArticleBundle:FragmentAdmin:edit_article.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
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
     * {@inheritdoc}
     *
     * @param ArticleInterface $object
     */
    public function prePersist($object)
    {
        $this->sort($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $fragmentClass = $this->fragmentClass;
        $subject = $this->getSubject();

        $formMapper
            ->with('General', array('class' => 'col-md-8'))
                ->add('title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'attr' => array('maxlength' => 255),
                ))
                ->add('subtitle', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'required' => false,
                    'attr' => array('maxlength' => 255),
                ))
                ->add('abstract', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                    'required' => false,
                ))
            ->end()

            ->with('Publication', array('class' => 'col-md-4'))
                ->add('status', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'choices' => $this->isGranted('ROLE_ARTICLE_PUBLISH') ?
                        AbstractArticle::getStatuses() : AbstractArticle::getContributorStatus(),
                    'attr' => array('class' => 'full-width'),
                    'choices_as_values' => false,
                ))
                ->add('publicationStartsAt', 'Sonata\CoreBundle\Form\Type\DateTimePickerType', array(
                    'format' => 'dd/MM/yyyy HH:mm',
                    'datepicker_use_button' => false,
                    'dp_side_by_side' => true,
                    'dp_language' => 'fr',
                    'required' => false,
                ))
                ->add('publicationEndsAt', 'Sonata\CoreBundle\Form\Type\DateTimePickerType', array(
                    'format' => 'dd/MM/yyyy HH:mm',
                    'datepicker_use_button' => false,
                    'dp_side_by_side' => true,
                    'dp_language' => 'fr',
                    'required' => false,
                ))
            ->end()

            ->with('Tags', array('class' => 'col-md-6'))
                ->add('tags', 'sonata_type_model_autocomplete', array(
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'label' => false,
                    'attr' => array('class' => 'show'),
                    'callback' => function (AdminInterface $admin, $property, $searchText) {
                        $datagrid = $admin->getDatagrid();
                        $datagrid->setValue($property, null, $searchText);
                        $datagrid->setValue('enabled', null, true);
                    },
                ))
            ->end()

            ->with('Categories', array('class' => 'col-md-6'))
                ->add('categories', 'sonata_type_model_autocomplete', array(
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'label' => false,
                    'attr' => array('class' => 'show'),
                    'callback' => function (AdminInterface $admin, $property, $searchText) {
                        $datagrid = $admin->getDatagrid();
                        $datagrid->setValue($property, null, $searchText);
                        $datagrid->setValue('enabled', null, true);
                    },
                ))
            ->end()

            ->with('Fragments', array('class' => 'col-md-12'))
                ->add('fragments', 'sonata_type_collection', array(
                    'cascade_validation' => true,
                    'by_reference' => false,
                    'label' => false,
                    // callback of mapping fragment with the one selected on the list
                    'pre_bind_data_callback' => function ($value) use ($fragmentClass, $subject) {
                        $fragment = null;
                        // existing fragment case
                        foreach ($subject->getFragments() as $existingFragment) {
                            if (!is_null($existingFragment->getId()) && $existingFragment->getId() == $value['id']) {
                                $fragment = $existingFragment;
                                break;
                            }
                        }
                        // new fragment case
                        if (!$fragment) {
                            $fragment = (new $fragmentClass())
                                ->setType($value['type'])
                                ->setEnabled(isset($value['enabled']) ? (bool) $value['enabled'] : false)
                                ->setPosition($value['position'] ?: 1)
                                ->setSettings((isset($value['settings']) && is_array($value['settings'])) ? $value['settings'] : array());

                            $subject->addFragment($fragment);
                        }

                        return $fragment;
                    },
                ), array(
                    'sortable' => 'position',
                ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', 'text', array(
                'sortable' => true,
            ))
            ->add('updatedAt', 'datetime', array(
                'sortable' => true,
            ))
            ->add('status', 'choice', array(
                'sortable' => true,
                'choices' => AbstractArticle::getStatuses(),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title', 'text')
            ->add('subtitle', 'text')
            ->add('abstract', 'textarea')
        ;
    }

    /**
     * Sorting the fragments depending on position.
     *
     * @param ArticleInterface $article
     */
    private function sort(ArticleInterface $article)
    {
        $fragments = $article->getFragments()->toArray();

        usort($fragments, function (FragmentInterface $a, FragmentInterface $b) {
            return $a->getPosition() - $b->getPosition();
        });

        $article->setFragments(new ArrayCollection($fragments));
    }
}
