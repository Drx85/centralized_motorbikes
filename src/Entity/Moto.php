<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MotoRepository::class)
 * @UniqueEntity("name")
 */
class Moto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $cylinder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engineCooling;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engineDistribution;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $volumetricRatio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $starter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maxTorque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gearNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clutch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $finalDrive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frame;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $casterAngle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $casterTrail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $wheelbase;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frontSuspension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rearSuspension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frontBrake;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rearBrake;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frontTire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backTire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $LxlxH;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seatHeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groundClearance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fuelCapacity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $oilCapacity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="motos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="motos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fuelConsumption;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $horsePower;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fuelSystem;

    public function getId(): ?int
    {
        return $this->id;
    }
	
	public function setId(int $id): self
	{
		$this->id = $id;
		
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCylinder(): ?int
    {
        return $this->cylinder;
    }

    public function setCylinder(?int $cylinder): self
    {
        $this->cylinder = $cylinder;

        return $this;
    }

    public function getEngineCooling(): ?string
    {
        return $this->engineCooling;
    }

    public function setEngineCooling(?string $engineCooling): self
    {
        $this->engineCooling = $engineCooling;

        return $this;
    }

    public function getEngineDistribution(): ?string
    {
        return $this->engineDistribution;
    }

    public function setEngineDistribution(?string $engineDistribution): self
    {
        $this->engineDistribution = $engineDistribution;

        return $this;
    }

    public function getVolumetricRatio(): ?string
    {
        return $this->volumetricRatio;
    }

    public function setVolumetricRatio(?string $volumetricRatio): self
    {
        $this->volumetricRatio = $volumetricRatio;

        return $this;
    }

    public function getStarter(): ?string
    {
        return $this->starter;
    }

    public function setStarter(?string $starter): self
    {
        $this->starter = $starter;

        return $this;
    }

    public function getMaxTorque(): ?string
    {
        return $this->maxTorque;
    }

    public function setMaxTorque(?string $maxTorque): self
    {
        $this->maxTorque = $maxTorque;

        return $this;
    }

    public function getGearNumber(): ?string
    {
        return $this->gearNumber;
    }

    public function setGearNumber(?string $gearNumber): self
    {
        $this->gearNumber = $gearNumber;

        return $this;
    }

    public function getClutch(): ?string
    {
        return $this->clutch;
    }

    public function setClutch(?string $clutch): self
    {
        $this->clutch = $clutch;

        return $this;
    }

    public function getFinalDrive(): ?string
    {
        return $this->finalDrive;
    }

    public function setFinalDrive(?string $finalDrive): self
    {
        $this->finalDrive = $finalDrive;

        return $this;
    }

    public function getFrame(): ?string
    {
        return $this->frame;
    }

    public function setFrame(?string $frame): self
    {
        $this->frame = $frame;

        return $this;
    }

    public function getCasterAngle(): ?string
    {
        return $this->casterAngle;
    }

    public function setCasterAngle(?string $casterAngle): self
    {
        $this->casterAngle = $casterAngle;

        return $this;
    }

    public function getCasterTrail(): ?string
    {
        return $this->casterTrail;
    }

    public function setCasterTrail(?string $casterTrail): self
    {
        $this->casterTrail = $casterTrail;

        return $this;
    }

    public function getWheelbase(): ?string
    {
        return $this->wheelbase;
    }

    public function setWheelbase(?string $wheelbase): self
    {
        $this->wheelbase = $wheelbase;

        return $this;
    }

    public function getFrontSuspension(): ?string
    {
        return $this->frontSuspension;
    }

    public function setFrontSuspension(?string $frontSuspension): self
    {
        $this->frontSuspension = $frontSuspension;

        return $this;
    }

    public function getRearSuspension(): ?string
    {
        return $this->rearSuspension;
    }

    public function setRearSuspension(?string $rearSuspension): self
    {
        $this->rearSuspension = $rearSuspension;

        return $this;
    }

    public function getFrontBrake(): ?string
    {
        return $this->frontBrake;
    }

    public function setFrontBrake(?string $frontBrake): self
    {
        $this->frontBrake = $frontBrake;

        return $this;
    }

    public function getRearBrake(): ?string
    {
        return $this->rearBrake;
    }

    public function setRearBrake(?string $rearBrake): self
    {
        $this->rearBrake = $rearBrake;

        return $this;
    }

    public function getFrontTire(): ?string
    {
        return $this->frontTire;
    }

    public function setFrontTire(?string $frontTire): self
    {
        $this->frontTire = $frontTire;

        return $this;
    }

    public function getBackTire(): ?string
    {
        return $this->backTire;
    }

    public function setBackTire(?string $backTire): self
    {
        $this->backTire = $backTire;

        return $this;
    }

    public function getLxlxH(): ?string
    {
        return $this->LxlxH;
    }

    public function setLxlxH(?string $LxlxH): self
    {
        $this->LxlxH = $LxlxH;

        return $this;
    }

    public function getSeatHeight(): ?string
    {
        return $this->seatHeight;
    }

    public function setSeatHeight(?string $seatHeight): self
    {
        $this->seatHeight = $seatHeight;

        return $this;
    }

    public function getGroundClearance(): ?string
    {
        return $this->groundClearance;
    }

    public function setGroundClearance(?string $groundClearance): self
    {
        $this->groundClearance = $groundClearance;

        return $this;
    }

    public function getFuelCapacity(): ?string
    {
        return $this->fuelCapacity;
    }

    public function setFuelCapacity(?string $fuelCapacity): self
    {
        $this->fuelCapacity = $fuelCapacity;

        return $this;
    }

    public function getOilCapacity(): ?string
    {
        return $this->oilCapacity;
    }

    public function setOilCapacity(?string $oilCapacity): self
    {
        $this->oilCapacity = $oilCapacity;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFuelConsumption(): ?string
    {
        return $this->fuelConsumption;
    }

    public function setFuelConsumption(?string $fuelConsumption): self
    {
        $this->fuelConsumption = $fuelConsumption;

        return $this;
    }

    public function getHorsePower(): ?string
    {
        return $this->horsePower;
    }

    public function setHorsePower(?string $horsePower): self
    {
        $this->horsePower = $horsePower;

        return $this;
    }

    public function getFuelSystem(): ?string
    {
        return $this->fuelSystem;
    }

    public function setFuelSystem(?string $fuelSystem): self
    {
        $this->fuelSystem = $fuelSystem;

        return $this;
    }
}
