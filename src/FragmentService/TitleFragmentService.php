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
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class TitleFragmentService extends AbstractFragmentService
{
    public function buildForm(FormMapper $form, FragmentInterface $fragment): void
    {
        $form->add('fields', ImmutableArrayType::class, [
            'keys' => [
                ['text', TextType::class, [
                    'label' => 'Title',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                    ],
                ]],
            ],
            'label' => false,
        ]);
    }

    /**
     * @param FragmentInterface $object
     */
    public function validate(ErrorElement $errorElement, $object): void
    {
        if (empty($object->getField('text'))) {
            $errorElement
                ->addViolation('Fragment Title - `Text` must not be empty')
            ;
        }

        if (\strlen($object->getField('text')) > 255) {
            $errorElement
                ->addViolation('Fragment Text - `Text` must not be longer than 255 characters.')
            ;
        }
    }

    public function getTemplate(): string
    {
        return '@SonataArticle/Fragment/fragment_title.html.twig';
    }
}
