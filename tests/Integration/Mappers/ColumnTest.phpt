<?php
declare(strict_types=1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace FreezyBee\DoctrineFormMapper\Tests\Integration\Mappers;

require __DIR__ . '/../../bootstrap.php';

use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use FreezyBee\DoctrineFormMapper\Mappers\Column;
use FreezyBee\DoctrineFormMapper\Tests\Mock\Entity\Tag;
use FreezyBee\DoctrineFormMapper\Tests\Mock\Entity\TestDate;
use FreezyBee\DoctrineFormMapper\Tests\Mock\EntityManagerTrait;
use Nette\ComponentModel\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextInput;
use Tester\Assert;
use Tester\TestCase;

/**
 * @testCase
 */
class ColumnTest extends TestCase
{
    use EntityManagerTrait;

    /** @var Column */
    private $mapper;

    /**
     *
     */
    public function setUp()
    {
        $mapper = new DoctrineFormMapper($this->getEntityManager());
        $mapper->addMapper(new Column($mapper));
        $this->mapper = new Column($mapper);
    }

    /**
     *
     */
    public function testLoad()
    {
        $em = $this->getEntityManager();

        $tag = $em->find(Tag::class, 1001);
        $meta = $em->getClassMetadata(Tag::class);

        $component = new TextInput;
        $component->setParent(new Container, 'name');

        $result = $this->mapper->load($meta, $component, $tag);
        Assert::true($result);
        Assert::same('tag name1', $component->getValue());
    }

    /**
     *
     */
    public function testLoadNullableScalar()
    {
        $tag = new Tag;
        $meta = $this->getEntityManager()->getClassMetadata(Tag::class);

        $component = new TextInput;
        $component->setParent(new Container, 'name');

        $result = $this->mapper->load($meta, $component, $tag);
        Assert::true($result);
        Assert::same('', $component->getValue());
    }

    /**
     *
     */
    public function testLoadNullableObject()
    {
        $classReflection = new \ReflectionClass(TestDate::class);
        $testDate = $classReflection->newInstanceWithoutConstructor();
        $meta = $this->getEntityManager()->getClassMetadata(TestDate::class);

        $component = new TextInput;
        $component->setParent(new Container, 'date');

        $result = $this->mapper->load($meta, $component, $testDate);
        Assert::true($result);
        Assert::same('', $component->getValue());
    }

    /**
     *
     */
    public function testLoadNullableException()
    {
        Assert::exception(function () {
            $tag = new Tag;
            $tag->setName(777);
            $meta = $this->getEntityManager()->getClassMetadata(Tag::class);

            $component = new TextInput;
            $component->setParent(new Container, 'name');

            $this->mapper->load($meta, $component, $tag);
        }, \TypeError::class, '#must be of the type string, int(eger)? returned$#');
    }

    /**
     *
     */
    public function testLoadNonExistsField()
    {
        $tag = new Tag;
        $meta = $this->getEntityManager()->getClassMetadata(Tag::class);

        $component = new TextInput;
        $component->setParent(new Container, 'namee');

        $result = $this->mapper->load($meta, $component, $tag);
        Assert::false($result);
    }

    /**
     *
     */
    public function testSave()
    {
        $tag = new Tag;
        $meta = $this->getEntityManager()->getClassMetadata(Tag::class);

        $testName = 'nameY';

        $component = new TextInput;
        $component->setParent(new Container, 'name');
        $component->setValue($testName);

        $result = $this->mapper->save($meta, $component, $tag);
        Assert::true($result);
        Assert::same($testName, $tag->getName());
    }

    /**
     *
     */
    public function testRunWithButton()
    {
        $tag = new Tag;
        $meta = $this->getEntityManager()->getClassMetadata(Tag::class);
        $btn = new SubmitButton;

        Assert::false($this->mapper->load($meta, $btn, $tag));
        Assert::false($this->mapper->save($meta, $btn, $tag));
    }
}

(new ColumnTest)->run();
