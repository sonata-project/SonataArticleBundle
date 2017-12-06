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

namespace Sonata\ArticleBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class FragmentAdmin extends AbstractAdmin
{
    /**
     * @var FragmentServiceInterface[]
     */
    private $fragmentServices = [];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @param array $fragmentServices
     */
    public function setFragmentServices(array $fragmentServices): void
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
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        if ('edit' === $name) {
            if ($this->getRequest()->get('type')) {
                return $this->getService($this->getRequest()->get('type'))->getEditTemplate();
            }

            return 'SonataArticleBundle:FragmentAdmin:form.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();
        $this->formOptions['csrf_protection'] = false;

        // Our form will be built without knowing of its parent (article), so we need to specify it in its name
        $formBuilder = $this->getFormContractor()->getFormBuilder(
            $this->getRequest()->get('elementId').'_'.$this->getRequest()->get('fragCount', 0),
            $this->formOptions
        );

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRoute($name)
    {
        if ('create' === $name) {
            return true;
        }

        return parent::hasRoute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object->getId().' - '.$object->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        if ($this->request && $this->request->get('type')) {
            $object->setType($this->request->get('type'));
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object): void
    {
        $this->getService($object->getType())->preUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object): void
    {
        $this->getService($object->getType())->postUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object): void
    {
        $this->getService($object->getType())->prePersist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object): void
    {
        $this->getService($object->getType())->postPersist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object): void
    {
        $this->getService($object->getType())->preRemove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove($object): void
    {
        $this->getService($object->getType())->postRemove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        return [
            'type' => $this->getRequest()->get('type'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object): void
    {
        $this->fragmentServices[$object->getType()]->validate($errorElement, $object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('view', $this->getRouterIdParameter().'/view');
        $collection->add('form', 'form');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('type')
            ->add('enabled')
            ->add('updatedAt')
            ->add('position')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('enabled')
            ->add('updatedAt')
            ->add('position')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('id', HiddenType::class);
        $formMapper->add('enabled', HiddenType::class);
        $formMapper->add('position', HiddenType::class);
        $formMapper->add('type', HiddenType::class, ['read_only' => true]);

        if (!is_object($this->getSubject())) {
            return;
        }

        $optionsResolver = new OptionsResolver();

        $service = $this->getService($this->getSubject()->getType());

        $service->configureOptions($optionsResolver);
        $service->buildCreateForm($formMapper, $this->getSubject());

        $this->settings = $optionsResolver->resolve($this->settings);
    }

    /**
     * @param string $type
     *
     * @throws \RuntimeException
     *
     * @return FragmentServiceInterface
     */
    protected function getService($type)
    {
        if (!array_key_exists($type, $this->fragmentServices)) {
            throw new \RuntimeException(sprintf('Fragment service for type `%s` is not handled by this admin', $type));
        }

        return $this->fragmentServices[$type];
    }
}
