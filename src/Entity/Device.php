<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Attributes as OA;

/**
 * @Hateoas\Relation("self",
 *      href = @Hateoas\Route("detailDevices", parameters = { "id" = "expr(object.getId())" }))
 */
#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: 'The unique identifier of the device.')]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[OA\Property(description: 'The model name of the device (e.g. iPhone 3GS).', maxLength: 80)]
    private ?string $modelName = null;

    #[ORM\Column(length: 80)]
    #[OA\Property(description: 'The manufacturer of the device (e.g. Apple).', maxLength: 80)]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 80)]
    #[OA\Property(description: 'The platform of the device (e.g. iOS 17).', maxLength: 80)]
    private ?string $platform = null;

    #[ORM\Column(length: 50)]
    #[OA\Property(description: 'The serial number of the device (e.g. SJMZOmtU0csrv4R).', maxLength: 50)]
    private ?string $serialNumber = null;

    #[ORM\Column]
    #[OA\Property(description: 'The version of the user (e.g. 001).')]
    private ?int $version = null;

    #[ORM\Column]
    #[OA\Property(description: 'The build number of the user (e.g. 123).')]
    private ?int $buildNumber = null;


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


}
