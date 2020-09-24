<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle(),
            new Knp\FcTestBundle\KnpFcTestBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $configFile = 'config3-4.yml';
        if (version_compare(self::VERSION, "5.0", ">=")) {
            $configFile = 'config5.yml';
        }
        $loader->load($this->getProjectDir() . '/app/config/' . $configFile);
    }

    public function getProjectDir()
    {
        return realpath(__DIR__ . '/../');
    }

    /**
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        $params = parent::getKernelParameters();

        if ( ! isset($params['kernel.root_dir'])) {
            $params['kernel.root_dir'] = $params['kernel.project_dir'] . DIRECTORY_SEPARATOR . 'app';
        }

        return $params;
    }
}
