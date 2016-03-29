<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ExtensionLoader
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Load a symfony Extension
     *
     * @param ExtensionInterface $extension
     * @param array $configs
     */
    public function load(ExtensionInterface $extension, array $configs)
    {
        $extension->load($configs, $this->container);
    }
}
