<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Attributes as OA;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Hateoas\Relation("self",
 *      href = @Hateoas\Route("detailDevices", parameters = { "id" = "expr(object.getId())" }),
 * )
 * @Hateoas\Relation("delete",
 *       href = @Hateoas\Route("deleteDevices",parameters = { "id" = "expr(object.getId())" }),
 *       exclusion = @Hateoas\Exclusion(excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 * @Hateoas\Relation("update",
 *       href = @Hateoas\Route("updateDevices",parameters = { "id" = "expr(object.getId())" }),
 *       exclusion = @Hateoas\Exclusion(excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 */

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: 'The unique identifier of the device.')]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'The model name is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The model name must be at least {{ limit }} characters', maxMessage: 'The model name cannot be more than {{ limit }} characters')]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The model name of the device (e.g. iPhone 3GS).', maxLength: 80)]
    private ?string $modelName = null;

    #[Assert\NotBlank(message: 'The manufacturer is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The manufacturer must be at least {{ limit }} characters', maxMessage: 'The manufacturer cannot be more than {{ limit }} characters')]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The manufacturer of the device (e.g. Apple).', maxLength: 80)]
    private ?string $manufacturer = null;

    #[Assert\NotBlank(message: 'The platform is required.')]
    #[Assert\Length(min: 1, max: 80, minMessage: 'The platform must be at least {{ limit }} characters', maxMessage: 'The platform cannot be more than {{ limit }} characters')]
    #[ORM\Column(type: Types::STRING, length: 80)]
    #[OA\Property(description: 'The platform of the device (e.g. iOS 17).', maxLength: 80)]
    private ?string $platform = null;

    #[Assert\NotBlank(message: 'The serial number is required.')]
    #[Assert\Length(min: 1, max: 50, minMessage: 'The serial number must be at least {{ limit }} characters', maxMessage: 'The serial number cannot be more than {{ limit }} characters')]
    #[ORM\Column(type: Types::STRING, length: 50)]
    #[OA\Property(description: 'The serial number of the device (e.g. SJMZOmtU0csrv4R).', maxLength: 50)]
    private ?string $serialNumber = null;

    #[Assert\NotBlank(message: 'The version is required.')]
    #[ORM\Column(type: Types::INTEGER)]
    #[OA\Property(description: 'The version of the user (e.g. 001).')]
    private ?int $version = null;

    #[Assert\NotBlank(message: 'The build number number is required.')]
    #[ORM\Column(type: Types::INTEGER)]
    #[OA\Property(description: 'The build number of the user (e.g. 123).')]
    private ?int $buildNumber = null;

    #[ORM\Column(type: 'datetime')]
    #[OA\Property(description: 'The date of creation of the device. No need to specify because it\'s automatic')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[OA\Property(description: 'The date of the modification of the device. No need to specify because it\'s automatic')]
    private ?\DateTimeInterface $updatedAt = null;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(?int $id): Device
    {
        $this->id = $id;
        return $this;
    }


    public function getModelName(): ?string
    {
        return $this->modelName;
    }


    public function setModelName(?string $modelName): Device
    {
        $this->modelName = $modelName;
        return $this;
    }


    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }


    public function setManufacturer(?string $manufacturer): Device
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }


    public function getPlatform(): ?string
    {
        return $this->platform;
    }


    public function setPlatform(?string $platform): Device
    {
        $this->platform = $platform;
        return $this;
    }


    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }


    public function setSerialNumber(?string $serialNumber): Device
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }


    public function getVersion(): ?int
    {
        return $this->version;
    }


    public function setVersion(?int $version): Device
    {
        $this->version = $version;
        return $this;
    }


    public function getBuildNumber(): ?int
    {
        return $this->buildNumber;
    }


    public function setBuildNumber(?int $buildNumber): Device
    {
        $this->buildNumber = $buildNumber;
        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }


    public function setCreatedAt(?\DateTimeInterface $createdAt): Device
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt(?\DateTimeInterface $updatedAt): Device
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}
