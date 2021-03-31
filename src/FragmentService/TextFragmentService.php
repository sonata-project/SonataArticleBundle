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
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class TextFragmentService extends AbstractFragmentService
{
    public function buildForm(FormMapper $form, FragmentInterface $fragment): void
    {
        $form->add('fields', ImmutableArrayType::class, [
            'keys' => [
                ['text', TextareaType::class, [
                    'label' => 'Text',
                    'constraints' => [
                        new NotBlank(),
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
                ->addViolation('Fragment Text - `Text` must not be empty');
        }
    }

    public function getTemplate(): string
    {
        return '@SonataArticle/Fragment/fragment_text.html.twig';
    }
}
