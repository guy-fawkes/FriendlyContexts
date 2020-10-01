<?php

namespace Knp\FriendlyContexts\Alice\Fixtures\Alice3;

use Knp\FriendlyContexts\Alice\Fixtures\Loader as LoaderInterface;
use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\FilesLoaderInterface;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\FixtureSet;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Loader\SimpleFilesLoader;
use Nelmio\Alice\Throwable\Exception\ObjectNotFoundException;

class Loader extends NativeLoader implements
    LoaderInterface
{
    /**
     * @var null|FixtureSet
     */
    private $fixtureSet = null;
    private $objects = null;
    private $loader;
    private $dataLoader;

    public function __construct()
    {
        parent::__construct();

        $this->loader = $this->createFilesLoader();
    }

    public function getCache()
    {
        if (null === $this->fixtureSet) {
            return array();
        }

        $cache = array();
        $fixtures = $this->fixtureSet->getFixtures();
        /** @var FixtureInterface $fixture */
        foreach ($fixtures as $fixture) {
            $spec = array();

            $properties = $fixture->getSpecs()->getProperties();
            foreach ($properties->getIterator() as $property) {
                $spec[] = $property->getValue();
            }

            $cache[] = array($spec, $this->objects[$fixture->getId()]);
        }

        return $cache;
    }

    public function clearCache()
    {
        $this->fixtureSet = null;
        $this->objects = null;
    }

    public function load($filename)
    {
        if ( ! is_array($filename)) {
            $filename = array($filename);
        }
        $this->objects = $this->loader->loadFiles($filename)->getObjects();
        $this->fixtureSet = $this->dataLoader->getLastFixtureSet();
        return $this->objects;
    }

    protected function createDataLoader() : DataLoaderInterface
    {
        return new DataLoader(
            $this->getFixtureBuilder(),
            $this->getGenerator()
        );
    }

    protected function createFilesLoader(): FilesLoaderInterface
    {
        if (null === $this->dataLoader) {
            $this->dataLoader = $this->createDataLoader();
        }

        return new SimpleFilesLoader(
            $this->getParser(),
            $this->dataLoader
        );
    }
}
