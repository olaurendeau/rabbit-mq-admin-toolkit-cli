<?php
namespace App;

use App\Lib\ErrorHandler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $baseNamespaceName Highest level namespace for the application
     */
    public function __construct($baseNamespaceName)
    {
        // our error handler
        ErrorHandler::register();

        // create and populate the container
        $this->container = new ContainerBuilder();
        $this->container->set('container', $this->container);

        // some useful paths
        $paths = array();
        $paths['root'] = __DIR__ . '/../../';
        $paths['config'] = $paths['root'] . 'app/config/';
        $paths['build'] = $paths['root'] . 'build/';
        $this->container->setParameter('paths', $paths);

        // the main config
        $loader = new YamlFileLoader($this->container, new FileLocator($paths['config']));
        $loader->load('config.yml');
        $loader->load('services.yml');

        // construct the application
        $app = $this->container->getParameter('application');
        $version = $this->container->getParameter('version');
        parent::__construct($app['name'],$version['current']);

        // and add commands to it
        $this->addConsoleCommands($baseNamespaceName);

        // may be we have some commands also
        $this->addConsoleCommands(__NAMESPACE__);
    }

    /**
     * Adds all existing console commands
     *
     * @param string $baseNamespaceName Base namespace for the commands
     */
    protected function addConsoleCommands($baseNamespaceName)
    {
        // get all namespaces from the composer autoload list
        $paths = $this->container->getParameter('paths');
        $namespaces = include $paths['root'] . '/vendor/composer/autoload_namespaces.php';
        // find the path for the namespace
        foreach ($namespaces as $namespace => $lookupPaths) {
            if ($namespace == $baseNamespaceName) {
                // add all existing commands
                foreach ($lookupPaths as $path) {
                    $commandPath = $path . '/' . $namespace . '/Command/';
                    if (is_dir($commandPath)) {
                        $files = Finder::create()->files()->name('*Command.php')->in($path . '/' . $namespace . '/Command/');
                        foreach ($files as $file) {
                            $className = $file->getBasename('.php'); // strip .php extension
                            $r = new \ReflectionClass($baseNamespaceName . '\Command' . '\\' . $className);
                            if (!$r->isAbstract()) {
                                $command = $r->newInstance();
                                $command->setContainer($this->container);

                                $this->add($command);
                            }
                        }
                    }
                }
                break;
            }
        }
    }
}
