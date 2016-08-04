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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class TitleFragmentService extends AbstractFragmentService
{
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, FragmentInterface $fragment)
    {
        $form->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('text', 'text', array(
                    'label' => 'Title',
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 255)),
                    ),
                )),
            ),
            'label' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FragmentInterface $fragment, ExecutionContextInterface $context)
    {
        if (empty($fragment->getSettings()['text'])) {
            $context
                ->buildViolation('`Title` must not be empty')
                ->atPath('settings.text')
                ->addViolation()
            ;
        }
        if (strlen($fragment->getSetting('text')) > 255) {
            $context
                ->buildViolation('`Title` must not be longer than 255 characters.')
                ->atPath('settings.text')
                ->addViolation()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'SonataArticleBundle:Fragment:fragment_title.html.twig';
    }
}
