<?php


namespace App\Services;


use App\Entity\Document;

class DocumentFactory extends AbstractFactory
{
    public function getEntityName(): string
    {
        return Document::class;
    }
}