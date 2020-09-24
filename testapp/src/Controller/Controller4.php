<?php

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Controller is a simple implementation of a Controller.
 *
 * It provides methods to common features needed in controllers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Controller4 implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ControllerTrait;

    /**
     * Gets a container configuration parameter by its name.
     *
     * @param string $name The parameter name
     *
     * @return mixed
     *
     * @final since version 3.4
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
}
