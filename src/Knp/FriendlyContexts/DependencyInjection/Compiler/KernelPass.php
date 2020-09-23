<?php

namespace Knp\FriendlyContexts\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class KernelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->loadFileFromParameter($container, 'friendly.symfony_kernel.bootstrap');
        $this->loadFileFromParameter($container, 'friendly.symfony_kernel.path');

        if ($container->has('symfony2_extension.kernel')) {
            $alias = $container
                ->setAlias('friendly.symfony.kernel', 'symfony2_extension.kernel')
            ;
            if ( ! $alias instanceof Alias) {
                $alias = $container->getAlias('friendly.symfony.kernel');
            }
            $alias->setPublic(true);
        } elseif (null !== $class = $this->getKernelClass($container)) {
            $definition = new Definition($class);
            $definition
                ->addArgument($container->getParameter('friendly.symfony_kernel.env'))
                ->addArgument($container->getParameter('friendly.symfony_kernel.debug'))
            ;
            $container->setDefinition('friendly.symfony.kernel', $definition);
        }
    }

    protected function loadFileFromParameter(ContainerBuilder $container, $parameter)
    {
        $base  = $container->getParameter('paths.base');
        $param = $container->getParameter($parameter);
        if (file_exists($file = $base.DIRECTORY_SEPARATOR.$param)) {
            require_once($file);
        } elseif (file_exists($param)) {
            require_once($param);
        }
    }

    protected function getKernelClass(ContainerBuilder $container)
    {
        $class = $container->getParameter('friendly.symfony_kernel.class');

        return class_exists($class) ? $class : null;
    }
}
