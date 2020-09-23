<?php


namespace Knp\FriendlyContexts\Alice\Fixtures;


use Knp\FriendlyContexts\Alice\ProviderResolver;

class LoaderFactory
{
    /**
     * @return Loader
     */
    public function create($locale, ProviderResolver $providers) : Loader
    {
        if (class_exists('Nelmio\Alice\Loader\NativeLoader')) {
            return new Alice3\Loader();
        }

        return new Alice2\Loader($locale, $providers);
    }
}