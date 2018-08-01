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

namespace Sonata\ArticleBundle\Tests\Admin;

use Doctrine\Common\Inflector\Inflector;
use Knp\Menu\FactoryInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Builder\FormContractorInterface;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Builder\RouteBuilderInterface;
use Sonata\AdminBundle\Builder\ShowBuilderInterface;
use Sonata\AdminBundle\Route\RouteGeneratorInterface;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
abstract class AdminTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ModelManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelManagerMock;

    /**
     * @var FormContractorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formContractorMock;

    /**
     * @var ShowBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $showBuilderMock;

    /**
     * @var ListBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $listBuilderMock;

    /**
     * @var DatagridBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $datagridBuilderMock;

    /**
     * @var TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $poolMock;

    /**
     * @var RouteGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $routeGeneratorMock;

    /**
     * @var ValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var SecurityHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $securityHandlerMock;

    /**
     * @var FactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuFactoryMock;

    /**
     * @var RouteBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $routeBuilderMock;

    /**
     * @var LabelTranslatorStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $labelTranslatorStrategyMock;

    /**
     * Default dummy variables.
     *
     * @var string
     */
    protected $dummyAdminId = 'sonata.admin';
    protected $dummyController = 'SonataAdminBundle:CRUD';

    /**
     * Create mocks of all admin default services
     * and assign them to a property of the class.
     *
     * @param AdminInterface $admin
     */
    protected function mockDefaultServices(AdminInterface $admin): void
    {
        // Each element is composed of: original_service_key, propertyName, class
        $defaultServices = [
            ['model_manager', 'modelManagerMock', ModelManager::class],
            ['form_contractor', 'formContractorMock', FormContractorInterface::class],
            ['show_builder', 'showBuilderMock', ShowBuilderInterface::class],
            ['list_builder', 'listBuilderMock', ListBuilderInterface::class],
            ['datagrid_builder', 'datagridBuilderMock', DatagridBuilderInterface::class],
            ['translator', 'translatorMock', TranslatorInterface::class],
            ['configuration_pool', 'poolMock', Pool::class],
            ['route_generator', 'routeGeneratorMock', RouteGeneratorInterface::class],
            ['validator', 'validatorMock', ValidatorInterface::class],
            ['security_handler', 'securityHandlerMock', SecurityHandlerInterface::class],
            ['menu_factory', 'menuFactoryMock', FactoryInterface::class],
            ['route_builder', 'routeBuilderMock', RouteBuilderInterface::class],
            ['label_translator_strategy', 'labelTranslatorStrategyMock', LabelTranslatorStrategyInterface::class],
            ['template_registry', 'templateRegistry', MutableTemplateRegistryInterface::class],
        ];

        // Generate all mocks
        foreach ($defaultServices as $service) {
            $this->{$service[1]} = $this->getMockBuilder($service[2])->disableOriginalConstructor()->getMock();
        }

        $this->applyDefaults($admin, $defaultServices);
    }

    /**
     * Use this function to instantiate all default services as mocks in the Admin.
     *
     * @param AdminInterface $admin
     * @param array          $defaultServices
     */
    private function applyDefaults(AdminInterface $admin, array $defaultServices): void
    {
        // Add services to the admin
        foreach ($defaultServices as $service) {
            $method = 'set'.Inflector::classify($service[0]);

            if (method_exists($admin, $method)) {
                $admin->{$method}($this->{$service[1]});
            }
        }
    }
}
