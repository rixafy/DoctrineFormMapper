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
use FreezyBee\DoctrineFormMapper\Mappers\ManyToMany;
use FreezyBee\DoctrineFormMapper\Mappers\OneToOne;
use FreezyBee\DoctrineFormMapper\Tests\Mock\Entity\Address;
use FreezyBee\DoctrineFormMapper\Tests\Mock\Entity\Article;
use FreezyBee\DoctrineFormMapper\Tests\Mock\Entity\Author;
use FreezyBee\DoctrineFormMapper\Tests\Mock\EntityManagerTrait;
use Nette\ComponentModel\Container;
use Tester\Assert;
use Tester\TestCase;

/**
 * @testCase
 */
class OneToOneTest extends TestCase
{
    use EntityManagerTrait;

    /** @var ManyToMany */
    private $mapper;

    /**
     *
     */
    public function setUp()
    {
        $this->mapper = new OneToOne(new DoctrineFormMapper($this->getEntityManager()));
    }

    /**
     *
     */
    public function testLoad()
    {
        $em = $this->getEntityManager();
        $author = $em->find(Author::class, 11);
        $meta = $em->getClassMetadata(Author::class);

        $component = new \Nette\Forms\Container;
        $component->setParent(new Container, 'address');
        $textControl = $component->addText('street');

        $result = $this->mapper->load($meta, $component, $author);
        Assert::true($result);
        Assert::same('address street1', $textControl->getValue());
    }

    /**
     *
     */
    public function testLoadNonExistsField()
    {
        $article = new Author('', new Address);
        $meta = $this->getEntityManager()->getClassMetadata(Article::class);

        $component = new \Nette\Forms\Container;
        $component->setParent(new Container, 'address');
        $component->addText('streets');

        $result = $this->mapper->load($meta, $component, $article);
        Assert::false($result);
    }

    /**
     *
     */
    public function testSaveManaged()
    {
        $em = $this->getEntityManager();

        /** @var Author $author */
        $author = $em->find(Author::class, 11);
        $meta = $em->getClassMetadata(Author::class);

        $component = new \Nette\Forms\Container;
        $component->setParent(new Container, 'address');
        $textControl = $component->addText('street');

        $this->mapper->load($meta, $component, $author);
        Assert::same(1, $author->getAddress()->getId());

        $textControl->setValue('street name 3!!!');

        $result = $this->mapper->save($meta, $component, $author);
        Assert::true($result);

        $em->flush()->clear();

        $author = $em->find(Author::class, 11);
        Assert::same('street name 3!!!', $author->getAddress()->getStreet());
    }
}

(new OneToOneTest)->run();
