<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\Table(name="document",uniqueConstraints={@ORM\UniqueConstraint(name="cnp", columns={"CNP"})})
 */
class Document
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $documentId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $locality;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $streetNr;

    /**
     * @ORM\Column(type="integer")
     */
    private $hectare;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $CISeries;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $CINr;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $CNP;

    /**
     * @ORM\Column(type="integer")
     */
    private $cubeMeters;

    public function getDocumentId(): ?int
    {
        return $this->documentId;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNr(): ?string
    {
        return $this->streetNr;
    }

    public function setStreetNr(?string $streetNr): self
    {
        $this->streetNr = $streetNr;

        return $this;
    }

    public function getHectare(): ?int
    {
        return $this->hectare;
    }

    public function setHectare(int $hectare): self
    {
        $this->hectare = $hectare;

        return $this;
    }

    public function getCISeries(): ?string
    {
        return $this->CISeries;
    }

    public function setCISeries(string $CISeries): self
    {
        $this->CISeries = $CISeries;

        return $this;
    }

    public function getCINr(): ?string
    {
        return $this->CINr;
    }

    public function setCINr(string $CINr): self
    {
        $this->CINr = $CINr;

        return $this;
    }

    public function getCNP(): ?string
    {
        return $this->CNP;
    }

    public function setCNP(string $CNP): self
    {
        $this->CNP = $CNP;

        return $this;
    }

    public function getCubeMeters(): ?int
    {
        return $this->cubeMeters;
    }

    public function setCubeMeters(int $cubeMeters): self
    {
        $this->cubeMeters = $cubeMeters;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint( new UniqueEntity(['fields' => 'CNP']));
    }
}
