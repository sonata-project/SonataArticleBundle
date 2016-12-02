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
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface FragmentServiceInterface
{
    /**
     * Builds the edition form for the fragment.
     *
     * @param FormMapper        $form
     * @param FragmentInterface $fragment
     */
    public function buildEditForm(FormMapper $form, FragmentInterface $fragment);

    /**
     * Builds the creation form for the fragment.
     *
     * @param FormMapper        $form
     * @param FragmentInterface $fragment
     */
    public function buildCreateForm(FormMapper $form, FragmentInterface $fragment);

    /**
     * Validates the fragment (you'll need to add your violations through context).
     *
     * @param FragmentInterface         $fragment
     * @param ExecutionContextInterface $context
    /**
     * Validates the fragment (you'll need to add your violations through $errorElement).
     *
     * @param ErrorElement $errorElement
     * @param object       $object
     */
    public function validate(ErrorElement $errorElement, $object);

    /**
     * Returns the Fragment service readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Configure options for the fragment settings.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Gets edit template to render form.
     *
     * @return string
     */
    public function getEditTemplate();

    /**
     * Gets template to render fragment.
     *
     * @return string
     */
    public function getTemplate();
}
