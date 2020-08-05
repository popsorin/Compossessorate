<?php

namespace App\Services;

use ReflectionClass;
use ReflectionException;

abstract class AbstractFactory
{
    /**
     * @param string $filename
     * @return array
     * @throws ReflectionException
     */
    public function createFromFile(string $filename)
    {
        $handle = fopen($filename, "r");
        $reflection = new ReflectionClass($this->getEntityName());
        $entities = [];
        $properties = $reflection->getProperties();

        do {
            $entity = $reflection->newInstanceWithoutConstructor();
            foreach ($properties as $property) {
                $line = fgets($handle);
                $property->setAccessible(true);
                $property->setValue($entity, $line);
            }
            $entities[] = $entity;
        }while($line);
        fclose($handle);

        return $entities;
    }

    /**
     * @return string
     */
    public abstract function getEntityName(): string;
}