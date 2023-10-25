<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Since;
use OpenApi\Attributes as OA;

/**
 * @Hateoas\Relation("self",
 *      href = @Hateoas\Route("detailCustomers", parameters = { "id" = "expr(object.getId())" }),
 *      exclusion = @Hateoas\Exclusion(groups="getCustomers")
 * )
 * @Hateoas\Relation("delete",
 *       href = @Hateoas\Route("deleteCustomers",parameters = { "id" = "expr(object.getId())" }),
 *       exclusion = @Hateoas\Exclusion(groups="getCustomers", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 * @Hateoas\Relation("update",
 *       href = @Hateoas\Route("updateCustomers",parameters = { "id" = "expr(object.getId())" }),
 *       exclusion = @Hateoas\Exclusion(groups="getCustomers", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 */

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getCustomers'])]
    #[OA\Property(description: 'The unique identifier of the customer.')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?User $user = null;

    #[Assert\NotBlank(message: 'The first name is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The first name must be at least {{ limit }} characters', maxMessage: 'The first name cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The unique fist name of the customer.')]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: 'The last name is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The last name must be at least {{ limit }} characters', maxMessage: 'The last name cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The last name of the customer.')]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: 'The email is required.')]
    #[Assert\Length(min: 1, max: 180, minMessage: 'The email must be at least {{ limit }} characters', maxMessage: 'The email cannot be more than {{ limit }} characters')]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 180)]
    #[OA\Property(description: 'The email of the customer.')]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'The gender is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The gender must be at least {{ limit }} characters', maxMessage: 'The gender cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The gender of the customer.')]
    private ?string $gender = null;

    #[Assert\NotBlank(message: 'The date of birth is required.')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime')]
    #[OA\Property(description: 'The date of birth of the customer. (e.g. 2002-02-02)')]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Assert\NotBlank(message: 'The phone number is required.')]
    #[Assert\Length(min: 1, max: 20, minMessage: 'The phone number must be at least {{ limit }} characters', maxMessage: 'The phone number cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 20)]
    #[OA\Property(description: 'The phone number of the customer.')]
    private ?string $phoneNumber = null;

    #[Assert\NotBlank(message: 'The address is required.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'The address must be at least {{ limit }} characters', maxMessage: 'The address cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[OA\Property(description: 'The address of the customer.')]
    private ?string $address = null;

    #[Assert\NotBlank(message: 'The company is required.')]
    #[Assert\Length(min: 1, max: 50, minMessage: 'The company must be at least {{ limit }} characters', maxMessage: 'The company cannot be more than {{ limit }} characters')]
    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::STRING, length: 50)]
    #[OA\Property(description: 'The company of the customer.')]
    private ?string $company = null;

    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime')]
    #[OA\Property(description: 'The date of creation of the customer. No need to specify because it\'s automatic')]
    private ?\DateTimeInterface $createdAt;

    #[Groups(['getCustomers'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[OA\Property(description: 'The date of the modification of the customer. No need to specify because it\'s automatic')]
    private ?\DateTimeInterface $updatedAt = null;

    #[Groups(['getCustomers'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Since("2.0")]
    #[OA\Property(description: 'The comment of the customer. Since version 2.0')]
    private ?string $comment = null;
    //Accept -> application/json; version=1.0


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


    public function getComment(): ?string
    {
        return $this->comment;
    }


    public function setComment(?string $comment): Customer
    {
        $this->comment = $comment;
        return $this;
    }



}
