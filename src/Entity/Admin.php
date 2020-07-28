<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $adminId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     *
     * One-to many Unidirectional relation
     *
     * first name variable: the name of the join table
     * second name variable: the name of the foreign key that references the table from the "one"(left) side of the relation
     * third name variable: the name of the foreign key that references the table from the "many"(right) side of the relation
     *
     * first referencedColumnName variable: the primary key of the table from the "one"(left) side of the relation
     * second referencedColumnName variable: the primary key of the table from the "many"(right) side of the relation
     *
     * @ORM\ManyToMany(targetEntity="Document")
     * @ORM\JoinTable(name="admins_documents",
     *          joinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="admin_id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="document_id", unique=true)}
     *          )
     */
    private $documents;

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function setAdminId(int $adminId): self
    {
        $this->adminId = $adminId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
