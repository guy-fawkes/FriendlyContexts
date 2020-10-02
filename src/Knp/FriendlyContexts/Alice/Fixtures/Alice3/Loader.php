<?php

namespace Knp\FriendlyContexts\Alice\Fixtures\Alice3;

use Knp\FriendlyContexts\Alice\Fixtures\Loader as LoaderInterface;
use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\Definition\Fixture\FixtureId;
use Nelmio\Alice\Definition\Property;
use Nelmio\Alice\Definition\Value\FixtureReferenceValue;
use Nelmio\Alice\FilesLoaderInterface;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\FixtureSet;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Loader\SimpleFilesLoader;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\Throwable\Exception\ObjectNotFoundException;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    public function __construct(
        FilesLoaderInterface $filesLoader = null,
        DataLoader $dataLoader = null
    ) {
        parent::__construct();

        $this->dataLoader = $dataLoader;
        $this->loader = $filesLoader ?? $this->createFilesLoader();
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
            /** @var Property $property */
            foreach ($properties as $property) {
                $spec[ $property->getName() ] = $property->getValue();
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

        $this->finaliseAssociations();

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

    private function finaliseAssociations()
    {
        if ( ! $this->fixtureSet instanceof FixtureSet) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $objectBag = new ObjectBag($this->objects);
        $fixtureBag = $this->fixtureSet->getFixtures();

        /** @var FixtureInterface $fixture */
        foreach ($fixtureBag as $fixture) {
            $spec = $fixture->getSpecs();
            foreach ($spec->getProperties() as $property) {
                $value = $property->getValue();
                if ( ! $value instanceof FixtureReferenceValue) {
                    continue;
                }

                $owner = $objectBag->get($fixture)->getInstance();
                $target = $objectBag->get(
                    $fixtureBag->get($value->getValue())
                )->getInstance();

                if ($accessor->getValue($owner, $property->getName()) !== $target) {
                    $accessor->setValue($owner, $property->getName(), $target);
                }
            }
        }
    }
}
