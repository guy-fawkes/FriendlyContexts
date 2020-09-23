<?php

namespace Knp\FriendlyContexts\Alice\Fixtures\Alice3;

use Knp\FriendlyContexts\Alice\Fixtures\Loader as LoaderInterface;
use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\Loader\NativeLoader;

class Loader extends NativeLoader implements
    LoaderInterface
{
    private $fixtureSet;
    private $fixtureData;
    private $loader;
    private $dataLoader;

    public function __construct()
    {
        parent::__construct();

        $this->loader = $this->createFilesLoader();
        $this->dataLoader = $this->createDataLoader();
    }

    public function getCache()
    {
        $fixtureSet = $this->dataLoader->getLastFixtureSet();
        if (null === $fixtureSet) {
            return array();
        }

        $cache = array();
        $fixtures = $fixtureSet->getFixtures();
        foreach ($fixtures as $fixture) {
            $spec = array();

            $properties = $fixture->getSpecs()->getProperties();
            foreach ($properties->getIterator() as $property) {
                $spec[] = $property->getValue();
            }

            $cache[] = [$spec, $this->fixtureData[$fixture->getId()]];
        }

        return $cache;
    }

    public function clearCache()
    {
        $this->fixtureSet = null;
    }

    public function load($filename)
    {
        if ( ! is_array($filename)) {
            $filename = array($filename);
        }
        return $this->loader->loadFiles($filename)->getObjects();
    }

    protected function createDataLoader() : DataLoaderInterface
    {
        return new DataLoader(
            $this->getFixtureBuilder(),
            $this->getGenerator()
        );
    }
}
