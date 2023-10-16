<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getCustomers'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[Groups(['getCustomers'])]
    private ?User $user = null;

    #[Assert\NotBlank(message: 'The first name is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The first name must be at least {{ limit }} characters', maxMessage: 'The first name cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 80)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: 'The last name is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The last name must be at least {{ limit }} characters', maxMessage: 'The last name cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 80)]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: 'The email is required.')]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'The gender is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 80)]
    private ?string $gender = null;

    #[Assert\NotBlank(message: 'The date of birth is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Assert\NotBlank(message: 'The phone number is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[Assert\NotBlank(message: 'The address is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[Assert\NotBlank(message: 'The company is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(length: 50)]
    private ?string $company = null;

    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }


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


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt(?\DateTimeInterface $updatedAt): Customer
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}
