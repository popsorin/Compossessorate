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
        $line = stream_get_line($handle, 100, "\n");
        while($line) {
            $entity = $reflection->newInstanceWithoutConstructor();
            foreach ($properties as $property) {
                $line = preg_replace("/,/i", '.', $line);
                $property->setAccessible(true);
                $line = (is_numeric($line)) ? (float)$line : $line;
                $property->setValue($entity, $line);
                $line = stream_get_line($handle, 100, "\n");
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