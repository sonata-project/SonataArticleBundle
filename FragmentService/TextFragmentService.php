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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class TextFragmentService extends AbstractFragmentService
{
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, FragmentInterface $fragment)
    {
        $form->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('text', 'textarea', array(
                    'label' => 'Text',
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )),
            ),
            'label' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if (empty($object->getSetting('text'))) {
            $errorElement
                ->addViolation('Fragment Text - `Text` must not be empty')
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'SonataArticleBundle:Fragment:fragment_text.html.twig';
    }
}
