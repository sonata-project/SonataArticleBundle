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
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
abstract class AbstractFragmentService implements FragmentServiceInterface
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function buildCreateForm(FormMapper $form, FragmentInterface $fragment): void
    {
        $this->buildEditForm($form, $fragment);
    }

    public function buildEditForm(FormMapper $form, FragmentInterface $fragment): void
    {
        // Add BO title
        $form->add('backofficeTitle');
        $this->buildForm($form, $fragment);
    }

    public function buildForm(FormMapper $form, FragmentInterface $fragment): void
    {
    }

    public function prePersist(FragmentInterface $fragment): void
    {
    }

    public function postPersist(FragmentInterface $fragment): void
    {
    }

    public function preUpdate(FragmentInterface $fragment): void
    {
    }

    public function postUpdate(FragmentInterface $fragment): void
    {
    }

    public function preRemove(FragmentInterface $fragment): void
    {
    }

    public function postRemove(FragmentInterface $fragment): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        if (empty($object->getBackofficeTitle())) {
            $errorElement
                ->addViolation(sprintf('Fragment %s - `Backoffice Title` must not be empty', $this->getName()));
        }
    }

    public function getEditTemplate(): string
    {
        return '@SonataArticle/FragmentAdmin/form.html.twig';
    }
}
