<?php

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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Hugo Briand <briand@ekino.com>
 */
final class FragmentAdmin extends AbstractAdmin
{
    /**
     * @var FragmentServiceInterface[]
     */
    private $fragmentServices = array();

    /**
     * @var array
     */
    private $settings = array();

    /**
     * @param array $fragmentServices
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
     * @param array $settings
     */
    final public function setSettings(array $settings)
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
    public function preUpdate($object)
    {
        $this->getService($object)->preUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $this->getService($object)->postUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->getService($object)->prePersist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        $this->getService($object)->postPersist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        $this->getService($object)->preRemove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove($object)
    {
        $this->getService($object)->postRemove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }

        return array(
            'type' => $this->getRequest()->get('type'),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('view', $this->getRouterIdParameter().'/view');
        $collection->add('form', 'form');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
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
    protected function configureShowFields(ShowMapper $showMapper)
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
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
        $formMapper->add('enabled', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
        $formMapper->add('position', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
        $formMapper->add('type', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array('read_only' => true));

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
     * @return FragmentServiceInterface
     *
     * @throws \RuntimeException
     */
    protected function getService($type)
    {
        if (!array_key_exists($type, $this->fragmentServices)) {
            throw new \RuntimeException(sprintf('Fragment service for type `%s` is not handled by this admin', $type));
        }

        return $this->fragmentServices[$type];
    }
}
