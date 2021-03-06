<?php
declare(strict_types=1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace FreezyBee\DoctrineFormMapper;

use Doctrine\ORM\Mapping\ClassMetadata;
use FreezyBee\DoctrineFormMapper\Exceptions\InvalidStateException;
use Nette\ComponentModel\Component;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IComponentMapper
{
    const ITEMS_TITLE = 'items.title';
    const ITEMS_FILTER = 'items.filter';
    const ITEMS_ORDER = 'items.order';

    /**
     * @param ClassMetadata $meta
     * @param Component $component
     * @param mixed $entity
     * @throws InvalidStateException
     * @return bool
     */
    public function load(ClassMetadata $meta, Component $component, $entity): bool;

    /**
     * @param ClassMetadata $meta
     * @param Component $component
     * @param mixed $entity
     * @throws InvalidStateException
     * @return bool
     */
    public function save(ClassMetadata $meta, Component $component, &$entity): bool;
}
