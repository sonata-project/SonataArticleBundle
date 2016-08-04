<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Helper;

use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\ArticleBundle\Model\FragmentInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Sylvain Rascar <rascar.sylvain@gmail.com>
 */
class FragmentHelper
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var FragmentServiceInterface[]
     */
    protected $fragmentServices = array();

    /**
     * FragmentHelper constructor.
     *
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param FragmentServiceInterface[] $fragmentServices
     */
    final public function setFragmentServices(array $fragmentServices)
    {
        $this->fragmentServices = $fragmentServices;
    }

    /**
     * @return FragmentServiceInterface[]
     */
    public function getFragmentServices()
    {
        return $this->fragmentServices;
    }

    /**
     * @param FragmentInterface $fragment
     *
     * @return string
     */
    public function render(FragmentInterface $fragment)
    {
        $type = $fragment->getType();

        if (!array_key_exists($type, $this->fragmentServices)) {
            throw new \RuntimeException(sprintf('Cannot render Fragment of type `%s`. Service not found.', $type));
        }

        return $this->templating->render(
            $this->fragmentServices[$type]->getTemplate(),
            array(
                'fragment' => $fragment,
                'fields' => $fragment->getSettings(),
            )
        );
    }
}
