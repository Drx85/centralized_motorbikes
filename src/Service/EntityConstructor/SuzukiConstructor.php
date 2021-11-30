<?php

namespace App\Service\EntityConstructor;

use App\Entity\Moto;
use App\Repository\BrandRepository;
use App\Repository\MotoRepository;
use App\Repository\TypeRepository;
use App\Service\Scrapper\SuzukiScrapper;
use Doctrine\ORM\EntityManagerInterface;

class SuzukiConstructor
{
	private mixed $scrappedSuzuki;
	private Moto|array $suzuki;
	
	public function __construct(SuzukiScrapper         $suzukiScrapper,
								EntityManagerInterface $manager,
								MotoRepository         $motoRepository,
								BrandRepository        $brandRepository,
								TypeRepository         $typeRepository
	)
	{
		$scrappedSuzukies = $suzukiScrapper->getArray();
		
		foreach ($scrappedSuzukies as $key => $this->scrappedSuzuki) {
			$this->suzuki = new Moto();
			$this->suzuki->setBrand($brandRepository->findOneBy(array('name' => 'Suzuki')));
			$this->suzuki->setType($typeRepository->findOneBy(array('name' => $this->scrappedSuzuki['type'])));
			$this->suzuki->setName($key);
			
			$this->addSpecsNeedTreatment();
			$this->addSpecs([
				'EngineDistribution' => 'Distribution :',
				'VolumetricRatio'    => 'Rapport Volumétrique :',
				'Starter'            => 'Démarreur :',
				'HorsePower'         => 'Puissance annoncée :',
				'FuelSystem'         => 'Alimentation :',
				'FuelConsumption'    => 'Consommation :',
				'MaxTorque'          => 'Couple Annoncé :',
				'GearNumber'         => 'Boite :',
				'Clutch'             => 'Embrayage :',
				'Frame'              => 'Cadre :',
				'CasterAngle'        => 'Angle de chasse :',
				'CasterTrail'        => 'Chasse :',
				'Wheelbase'          => 'Empattement :',
				'FrontSuspension'    => 'Suspension avant :',
				'RearSuspension'     => 'Suspension arrière :',
				'FrontBrake'         => 'Frein avant :',
				'RearBrake'          => 'Frein arrière :',
				'FrontTire'          => 'Pneu avant :',
				'BackTire'           => 'Pneu arrière :',
				'LxlxH'              => 'L x l x H :',
				'SeatHeight'         => 'Hauteur de selle :',
				'GroundClearance'    => 'Garde au sol :',
				'FuelCapacity'       => 'Essence :',
				'OilCapacity'        => 'Huile :',
				'Weight'             => 'Poids :'
			]);
			
			$oldMoto = $motoRepository->findOneBy(array('name' => $this->suzuki->getName()));
			
			if ($oldMoto instanceof Moto) {
				if ($this->suzuki->getName() === $oldMoto->getName()) {
					$this->suzuki->setId($oldMoto->getId());
					if ($this->suzuki != $oldMoto) {
//TODO: Update in DB existing Moto entity

//						$manager->persist($this->suzuki);
					}
				}
			} else {
				$manager->persist($this->suzuki);
			}
		}
		
		$manager->flush();
		dd($scrappedSuzukies);
	}
	
	/**
	 * Fill Moto entity using the given array specs.
	 *
	 * @param array $specs
	 */
	private function addSpecs(array $specs): void
	{
		foreach ($specs as $key => $spec) {
			$setterName = 'set' . $key;
			$spec = array_key_exists($spec, $this->scrappedSuzuki['characteristics']) ? $this->scrappedSuzuki['characteristics'][$spec] : 0;
			if ($spec) $this->suzuki->$setterName($spec);
		}
	}
	
	/**
	 * Fill Moto entity from data which need specifics treatments.
	 *
	 * Create new function for each specific treatment and call it in this function.
	 *
	 */
	private function addSpecsNeedTreatment(): void
	{
		$engine = array_key_exists('Type :', $this->scrappedSuzuki['characteristics']) ? $this->scrappedSuzuki['characteristics']['Type :'] : '';
		$this->addCylinders($engine);
		$this->addEngineCooling($engine);
		$this->addPrice();
	}
	
	private function addCylinders(string $engine): void
	{
		$cylinders = match (1) {
			preg_match('#(twin|bicylindre|2.cylindre)#i', $engine) => 2,
			preg_match('#4.cylindre#i', $engine) => 4,
			preg_match('#monocylindre#i', $engine) => 1,
			default => 0
		};
		if ($cylinders) $this->suzuki->setCylinder($cylinders);
	}
	
	private function addEngineCooling(string $engine): void
	{
		$engineCooling = match (1) {
			preg_match('#(refroidissement.liquide|refroidissement.par.eau)#i', $engine) => 'Liquide',
			preg_match('#refroidissement.par.ai#i', $engine) => 'Par air',
			default => 0
		};
		if ($engineCooling) $this->suzuki->setEngineCooling($engineCooling);
	}
	
	private function addPrice(): void
	{
		foreach ($this->scrappedSuzuki['characteristics'] as $value) {
			if (str_contains($value, '€')) {
				$value = preg_replace('#[^0-9.]#', '', $value);
				$this->suzuki->setPrice((int)$value);
				break;
			}
		}
	}
}
