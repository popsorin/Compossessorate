<?php

namespace App\Services;

use ReflectionClass;
use ReflectionException;

abstract class AbstractFactory
{
    /**
     * Create an object from a .txt file
     * There can be only one word on every line
     *
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
        $line = fgets($handle);
        while($line) {
            $entity = $reflection->newInstanceWithoutConstructor();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $property->setValue($entity, $line);
                $line = fgets($handle);
            }
            if($line) {
                $entities[] = $entity;
            }
        }
        fclose($handle);

        return $entities;
    }

    /**
     * @return string
     */
    public abstract function getEntityName(): string;
}