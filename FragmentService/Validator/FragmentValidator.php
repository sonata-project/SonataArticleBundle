<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\FragmentService\Validator;

use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FragmentValidator extends ConstraintValidator
{
    /**
     * @var FragmentServiceInterface[]
     */
    private $fragmentServices;

    /**
     * @param array $fragmentServices
     */
    public function __construct(array $fragmentServices)
    {
        $this->fragmentServices = $fragmentServices;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FragmentInterface) {
            throw new ValidatorException(
                sprintf('FragmentValidator can only be used on Fragment objects, instance of `%s` given',
                    is_object($value) ? get_class($value) : 'not an object'
                )
            );
        }

        if (!array_key_exists($value->getType(), $this->fragmentServices)) {
            throw new ValidatorException(
                sprintf('Fragments of type `%s` are not handled; only fragments of types `%s` are supported',
                    $value->getType(),
                    implode(', ', array_keys($this->fragmentServices))
                )
            );
        }

        $this->fragmentServices[$value->getType()]->validate($value, $this->context);
    }
}
