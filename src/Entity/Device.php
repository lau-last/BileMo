<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $modelName = null;

    #[ORM\Column]
    private ?int $buildNumber = null;

    #[ORM\Column(length: 80)]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 80)]
    private ?string $platform = null;

    #[ORM\Column(length: 50)]
    private ?string $serialNumber = null;

    #[ORM\Column]
    private ?int $version = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getModelName(): ?string
    {
        return $this->modelName;
    }


    public function setModelName(string $modelName): static
    {
        $this->modelName = $modelName;

        return $this;
    }


    public function getBuildNumber(): ?int
    {
        return $this->buildNumber;
    }


    public function setBuildNumber(int $buildNumber): static
    {
        $this->buildNumber = $buildNumber;

        return $this;
    }


    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }


    public function setManufacturer(string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }


    public function getPlatform(): ?string
    {
        return $this->platform;
    }


    public function setPlatform(string $platform): static
    {
        $this->platform = $platform;

        return $this;
    }


    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }


    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }


    public function getVersion(): ?int
    {
        return $this->version;
    }


    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }



}
