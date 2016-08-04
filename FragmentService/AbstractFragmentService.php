<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\FragmentService;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
abstract class AbstractFragmentService implements FragmentServiceInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    final public function buildCreateForm(FormMapper $form, FragmentInterface $fragment)
    {
        $this->buildEditForm($form, $fragment);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function prePersist(FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function postPersist(FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function preUpdate(FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function postUpdate(FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function preRemove(FragmentInterface $fragment)
    {
    }

    /**
     * @param FragmentInterface $fragment
     */
    public function postRemove(FragmentInterface $fragment)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FragmentInterface $fragment, ExecutionContextInterface $context)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getEditTemplate()
    {
        return 'SonataArticleBundle:FragmentAdmin:form.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getTemplate();
}
