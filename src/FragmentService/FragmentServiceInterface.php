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
     */
    public function buildEditForm(FormMapper $form, FragmentInterface $fragment): void;

    /**
     * Builds the creation form for the fragment.
     */
    public function buildCreateForm(FormMapper $form, FragmentInterface $fragment): void;

    /**
     * Builds the common part of creation|edition form for the fragment.
     */
    public function buildForm(FormMapper $form, FragmentInterface $fragment): void;

    /**
     * Validates the fragment (you'll need to add your violations through $errorElement).
     *
     * @param object $object
     */
    public function validate(ErrorElement $errorElement, $object): void;

    /**
     * Returns the Fragment service readable name.
     */
    public function getName(): string;

    /**
     * Configure options for the fragment settings.
     */
    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * Gets edit template to render form.
     */
    public function getEditTemplate(): string;

    /**
     * Gets template to render fragment.
     */
    public function getTemplate(): string;
}
