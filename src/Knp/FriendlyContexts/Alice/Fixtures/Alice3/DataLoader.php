<?php

namespace Knp\FriendlyContexts\Alice\Fixtures\Alice3;

use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\FixtureBuilderInterface;
use Nelmio\Alice\FixtureSet;
use Nelmio\Alice\GeneratorInterface;
use Nelmio\Alice\IsAServiceTrait;
use Nelmio\Alice\ObjectSet;

/**
 * Class SimpleDataLoader
 *
 * Data loader for use with Alice3
 *
 * @package Knp\FriendlyContexts\Alice\Fixtures
 */
class DataLoader implements DataLoaderInterface
{
    use IsAServiceTrait;

    /**
     * @var FixtureBuilderInterface
     */
    private $builder;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var null|FixtureSet
     */
    private $lastFixtureSet = null;

    public function __construct(FixtureBuilderInterface $fixtureBuilder, GeneratorInterface $generator)
    {
        $this->builder = $fixtureBuilder;
        $this->generator = $generator;
    }

    /**
     * @inheritdoc
     */
    public function loadData(array $data, array $parameters = array(), array $objects = array()) : ObjectSet
    {
        $this->lastFixtureSet = $this->builder->build($data, $parameters, $objects);
        return $this->generator->generate($this->lastFixtureSet);
    }

    /**
     * @internal
     */
    public function getLastFixtureSet()
    {
        return $this->lastFixtureSet;
    }
}