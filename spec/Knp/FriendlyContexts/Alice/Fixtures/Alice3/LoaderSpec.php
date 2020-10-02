<?php


namespace spec\Knp\FriendlyContexts\Alice\Fixtures\Alice3;


use Knp\FriendlyContexts\Alice\Fixtures\Alice3\DataLoader;
use Nelmio\Alice\Definition\Fixture\SimpleFixture;
use Nelmio\Alice\Definition\MethodCallBag;
use Nelmio\Alice\Definition\Property;
use Nelmio\Alice\Definition\PropertyBag;
use Nelmio\Alice\Definition\SpecificationBag;
use Nelmio\Alice\Definition\Value\FixtureReferenceValue;
use Nelmio\Alice\FilesLoaderInterface;
use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\FixtureSet;
use Nelmio\Alice\ObjectSet;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

class LoaderSpec extends ObjectBehavior
{
    /**
     * @var Prophet
     */
    private $prophet;

    public function let(
        FilesLoaderInterface $filesLoader,
        ObjectSet $generatedObjects,
        DataLoader $dataLoader,
        FixtureSet $fixtureSet
    ) {
        $filesLoader->loadFiles(Argument::any())->willReturn($generatedObjects);
        $dataLoader->getLastFixtureSet()->willReturn($fixtureSet);

        $this->prophet = new Prophet();

        $this->beConstructedWith(
            $filesLoader,
            $dataLoader
        );
    }

    protected function createProductFixture(string $id, string $name, string $category): FixtureInterface
    {
        $propertyBag = new PropertyBag();
        $propertyBag = $propertyBag->with(new Property('name', $name));
        $propertyBag = $propertyBag->with(new Property('category', new FixtureReferenceValue($category)));
        $propertyBag = $propertyBag->with(new Property('attribute', new FixtureReferenceValue('prod_attribute')));
        return $this->createFixture($id, ProductSpy::class, $propertyBag);
    }

    protected function createCategoryFixture(string $id, string $name): FixtureInterface
    {
        $propertyBag = new PropertyBag();
        $propertyBag = $propertyBag->with(new Property('name', $name));
        return $this->createFixture($id, Category::class, $propertyBag);
    }

    protected function createFixture(string $id, string $class, PropertyBag $propertyBag): SimpleFixture
    {
        return new SimpleFixture(
            $id,
            $class,
            new SpecificationBag(
                null,
                $propertyBag,
                new MethodCallBag()
            )
        );
    }

    /**
     * Sometimes fixtures files are loaded out of order. So, Alice can't set up
     * all associations correctly.
     * Our loader should give the objects a once over, looking for, and fixing,
     * incomplete associations before returning the objects.
     */
    public function it_should_complete_associations_between_objects_across_seperate_files(
        ObjectSet $generatedObjects,
        FixtureSet $fixtureSet,
        ProductSpy $productA,
        ProductSpy $productB,
        DataLoader $dataLoader
//        CategoryStub $categoryA,
//        CategoryStub $categoryB,
//        FixtureInterface $productAttribute
    ) {
        $categoryA = new CategoryStub();
        $categoryB = new CategoryStub();
        $productAttribute = new AttributeStub();
        $generatedObjects->getObjects()->willReturn(array(
            'product_a' => $productA,
            'product_b' => $productB,
            'category_a' => $categoryA,
            'category_b' => $categoryB,
            'prod_attribute' => $productAttribute
        ));
        $fixtureBag = new FixtureBag();
        $fixtureBag = $fixtureBag->with($this->createProductFixture('product_a', 'Product A', 'category_a'));
        $fixtureBag = $fixtureBag->with($this->createProductFixture('product_b', 'Product B', 'category_b'));
        $fixtureBag = $fixtureBag->with($this->createCategoryFixture('category_a', 'Category A'));
        $fixtureBag = $fixtureBag->with($this->createCategoryFixture('category_b', 'Category B'));
        $fixtureBag = $fixtureBag->with($this->createFixture('prod_attribute', AttributeStub::class, new PropertyBag()));
        $fixtureSet->getFixtures()->willReturn($fixtureBag);

        foreach (array($productA, $productB) as $productStub) {
            $productStub->getCategory()->willReturn(null);
            $productStub->getAttribute()->willReturn($productAttribute);
        }

        $files = array('products.yml', 'categories.yml');
        $this->load($files);

        $dataLoader->getLastFixtureSet()->shouldHaveBeenCalled();

        $productA->setCategory($categoryA)->shouldHaveBeenCalled();
        $productA->setName('Product A')->shouldNotHaveBeenCalled();
        $productA->setAttribute($productAttribute)->shouldNotHaveBeenCalled();

        $productB->setCategory($categoryB)->shouldHaveBeenCalled();
        $productB->setName('Product B')->shouldNotHaveBeenCalled();
        $productB->setAttribute($productAttribute)->shouldNotHaveBeenCalled();
    }
}

interface ProductSpy {
    public function getName(): string;
    public function setName(string $name);
    public function getCategory();
    public function setCategory(CategoryStub $cat);
    public function getAttribute(): AttributeStub;
    public function setAttribute(AttributeStub $attribute);
}

class AttributeStub {
    // Test should error if any methods are called on this class
}

class CategoryStub {
    // Test should error if any methods are called on this class
}