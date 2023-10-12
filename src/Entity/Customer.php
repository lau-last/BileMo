<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getCustomer"])]
    private ?int $id = null;

    #[Groups(["getCustomers"])]
    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?User $user = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 80)]
    private ?string $firstname = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 80)]
    private ?string $lastname = null;


    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 80)]
    private ?string $gender = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(length: 50)]
    private ?string $company = null;

    #[Groups(["getCustomers"])]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(?int $id): Customer
    {
        $this->id = $id;
        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }


    public function setUser(?User $user): Customer
    {
        $this->user = $user;
        return $this;
    }


    public function getFirstname(): ?string
    {
        return $this->firstname;
    }


    public function setFirstname(?string $firstname): Customer
    {
        $this->firstname = $firstname;
        return $this;
    }


    public function getLastname(): ?string
    {
        return $this->lastname;
    }


    public function setLastname(?string $lastname): Customer
    {
        $this->lastname = $lastname;
        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }


    public function setEmail(?string $email): Customer
    {
        $this->email = $email;
        return $this;
    }


    public function getGender(): ?string
    {
        return $this->gender;
    }


    public function setGender(?string $gender): Customer
    {
        $this->gender = $gender;
        return $this;
    }


    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }


    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): Customer
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }


    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }


    public function setPhoneNumber(?string $phoneNumber): Customer
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }


    public function getAddress(): ?string
    {
        return $this->address;
    }


    public function setAddress(?string $address): Customer
    {
        $this->address = $address;
        return $this;
    }


    public function getCompany(): ?string
    {
        return $this->company;
    }


    public function setCompany(?string $company): Customer
    {
        $this->company = $company;
        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }


    public function setCreatedAt(?\DateTimeInterface $createdAt): Customer
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}
