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

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
trait AdminFormFieldTestTrait
{
    /**
     * @param AdminInterface $admin
     *
     * @return FormMapper|\PHPUnit_Framework_MockObject_MockObject|FormMapper
     */
    protected function mockFormMapper(AdminInterface $admin)
    {
        /** @var FormMapper|\PHPUnit_Framework_MockObject_MockObject $formMapper */
        $formMapper = $this->createMock(FormMapper::class);
        /*
         * We use the form builder mock to navigate through successive calls to 'get'
         * in case of model transformer instantiation for example
         *
         * @var FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject $form
         */
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        /*
         * We create a public property fields that will store the fields added to the mapper
         * to validate configureFormFields method
         */
        $formMapper->fields = [];

        $formBuilder->expects($this->any())->method('get')->will($this->returnValue($formBuilder));

        $formMapper->expects($this->any())
            ->method('getAdmin')->will($this->returnValue($admin));
        $formMapper->expects($this->any())
            ->method('create')->will($this->returnCallback(function ($name, $type) {
                return [$name, $type];
            }));
        $formMapper->expects($this->any())
            ->method('getFormBuilder')->will($this->returnValue($formBuilder));
        // We don't care about the groups
        $formMapper->expects($this->any())
            ->method('with')->will($this->returnValue($formMapper));
        $formMapper->expects($this->any())
            ->method('end')->will($this->returnValue($formMapper));
        // We just store the generated fields in the fields attribute
        $formMapper->expects($this->any())
            ->method('add')->will($this->returnCallback(function ($name, $type, $config) use ($formMapper) {
                $formMapper->fields[] = [$name, $type, $config];

                return $formMapper;
            }));

        return $formMapper;
    }

    /**
     * @param FormMapper|\PHPUnit_Framework_MockObject_MockObject $formMapper The FormMapper mock
     * @param array                                               $fields     The of fields in the expected order
     *
     * @throws \ReflectionException
     */
    protected function expectInOrder($formMapper, array $fields): void
    {
        $admin = $formMapper->getAdmin();
        $this->invokeMethod($admin, 'configureFormFields', [$formMapper]);

        foreach ($fields as $index => $field) {
            $this->assertArrayHasKey($index, $formMapper->fields);
            // We validate at least name and type
            $this->assertSame([$field[0], $field[1]], [$formMapper->fields[$index][0], $formMapper->fields[$index][1]]);
        }
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object     instantiated object to run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
