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
use Symfony\Component\Form\FormBuilderInterface;
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

    public function setFragmentServices(array $fragmentServices): void
    {
        $this->fragmentServices = $fragmentServices;
    }

    /**
     * @return FragmentServiceInterface[]
     */
    public function getFragmentServices(): array
    {
        return $this->fragmentServices;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getTemplate($name): ?string
    {
        if ('edit' === $name) {
            if ($this->getRequest()->get('type')) {
                return $this->getService($this->getRequest()->get('type'))->getEditTemplate();
            }

            return '@SonataArticle/FragmentAdmin/form.html.twig';
        }

        return parent::getTemplate($name);
    }

    public function getFormBuilder(): FormBuilderInterface
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

    public function hasRoute($name): bool
    {
        if ('create' === $name) {
            return true;
        }

        return parent::hasRoute($name);
    }

    public function toString($object): string
    {
        return $object->getId().' - '.$object->getType();
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        if ($this->request && $this->request->get('type')) {
            $object->setType($this->request->get('type'));
        }

        return $object;
    }

    public function preUpdate($object): void
    {
        $this->getService($object->getType())->preUpdate($object);
    }

    public function postUpdate($object): void
    {
        $this->getService($object->getType())->postUpdate($object);
    }

    public function prePersist($object): void
    {
        $this->getService($object->getType())->prePersist($object);
    }

    public function postPersist($object): void
    {
        $this->getService($object->getType())->postPersist($object);
    }

    public function preRemove($object): void
    {
        $this->getService($object->getType())->preRemove($object);
    }

    public function postRemove($object): void
    {
        $this->getService($object->getType())->postRemove($object);
    }

    public function getPersistentParameters(): array
    {
        if (!$this->hasRequest()) {
            return [];
        }

        return [
            'type' => $this->getRequest()->get('type'),
        ];
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $this->fragmentServices[$object->getType()]->validate($errorElement, $object);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('view', $this->getRouterIdParameter().'/view');
        $collection->add('form', 'form');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('type')
            ->add('enabled')
            ->add('updatedAt')
            ->add('position')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('enabled')
            ->add('updatedAt')
            ->add('position')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('id', HiddenType::class);
        $formMapper->add('enabled', HiddenType::class);
        $formMapper->add('position', HiddenType::class);
        $formMapper->add('type', HiddenType::class, ['attr' => ['readonly' => true]]);

        if (!\is_object($this->getSubject())) {
            return;
        }

        $optionsResolver = new OptionsResolver();

        $service = $this->getService($this->getSubject()->getType());

        $service->configureOptions($optionsResolver);
        $service->buildCreateForm($formMapper, $this->getSubject());

        $this->settings = $optionsResolver->resolve($this->settings);
    }

    /**
     * @throws \RuntimeException
     */
    protected function getService(string $type): FragmentServiceInterface
    {
        if (!\array_key_exists($type, $this->fragmentServices)) {
            throw new \RuntimeException(sprintf('Fragment service for type `%s` is not handled by this admin', $type));
        }

        return $this->fragmentServices[$type];
    }
}
