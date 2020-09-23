<?php


namespace Knp\FriendlyContexts\Alice\Fixtures;


interface Loader
{
    public function getCache();

    public function clearCache();

    /**
     * Loads a fixture file
     *
     * @param string|array $dataOrFilename data array or filename
     */
    public function load($dataOrFilename);
}