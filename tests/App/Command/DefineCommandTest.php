<?php

namespace App\Command;

use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DefineCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;
    private $container;
    private $command;
    private $commandTester;

    private $configuration;
    private $handler;

    public function setUp()
    {
        $this->configuration = $this->prophesize('Ola\RabbitMqAdminToolkitBundle\VhostConfiguration');

        $this->handler = $this->prophesize('Ola\RabbitMqAdminToolkitBundle\VhostHandler');

        $this->application = new Application();

        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->container->get('ola_rabbit_mq_admin_toolkit.handler.vhost')->willReturn($this->handler->reveal());
        $this->container->getParameter('ola_rabbit_mq_admin_toolkit.silent_failure')->willReturn(false);
        $this->container->getParameter('ola_rabbit_mq_admin_toolkit.default_vhost')->willReturn('foo');

        $this->command = new DefineCommand();
        $this->command->setApplication($this->application);
        $this->command->setContainer($this->container->reveal());

        $this->commandTester = new CommandTester($this->command);
    }

    public function test_execute_withUnaccessibleFile()
    {
        $this->expectException('\InvalidArgumentException');

        $this->commandTester->execute(array('command' => 'define'));
    }

    public function test_execute()
    {
        $extensionLoader = $this->prophesize('App\DependencyInjection\ExtensionLoader');
        $extensionLoader->load(
            Argument::type('Ola\RabbitMqAdminToolkitBundle\DependencyInjection\OlaRabbitMqAdminToolkitExtension'),
            Argument::type('array')
        )->shouldBeCalled();

        $this->container->get('extension_loader')->willReturn($extensionLoader->reveal());

        $this->handler->exists($this->configuration)->willReturn(false);
        $this->handler->define($this->configuration)->shouldBeCalled();

        $this->container->has('ola_rabbit_mq_admin_toolkit.configuration.foo')->willReturn(true);
        $this->container->get('ola_rabbit_mq_admin_toolkit.configuration.foo')->willReturn($this->configuration->reveal());

        $this->commandTester->execute(array('command' => 'define', 'config-file' => __DIR__.'/../../Resources/.rabbit-mq-admin-toolkit.yml'));

        $this->assertContains('created', $this->commandTester->getDisplay());
    }
}
