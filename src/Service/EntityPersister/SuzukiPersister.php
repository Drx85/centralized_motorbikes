<?php

namespace App\Service\EntityPersister;

use App\Entity\Moto;
use App\Repository\BrandRepository;
use App\Repository\MotoRepository;
use App\Repository\TypeRepository;
use App\Service\Scrapper\SuzukiScrapper;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;

class SuzukiPersister
{
	private array $scrappedSuzukies;
	private mixed $scrappedSuzuki;
	private array $suzuki;
	
	public function __construct(
		SuzukiScrapper                 $suzukiScrapper,
		private EntityManagerInterface $manager,
		private MotoRepository         $motoRepository,
		private BrandRepository        $brandRepository,
		private TypeRepository         $typeRepository
	)
	{
		$this->scrappedSuzukies = $suzukiScrapper->getArray();
	}
	
	#[NoReturn] public function updateFromScrappedData()
	{
		foreach ($this->scrappedSuzukies as $key => $this->scrappedSuzuki) {
			$this->suzuki = [
				'Name'  => $key,
				'Brand' => $this->brandRepository->findOneBy(array('name' => 'Suzuki')),
				'Type'  => $this->typeRepository->findOneBy(array('name' => $this->scrappedSuzuki['type']))
			];
			
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
			
			$oldMoto = $this->motoRepository->findOneBy(array('name' => $this->suzuki['Name']));
			
			if ($oldMoto instanceof Moto) {
				$this->fillEntity($oldMoto);
			} else {
				$newSuzuki = new Moto();
				$this->fillEntity($newSuzuki);
				$this->manager->persist($newSuzuki);
			}
		}
		$this->manager->flush();
		dd($this->scrappedSuzukies);
	}
	
	/**
	 * Fill Moto entity of this class, using the given Moto entity.
	 *
	 * @param Moto $entity
	 */
	private function fillEntity(Moto $entity): void
	{
		foreach ($this->suzuki as $key => $spec) {
			$setterName = 'set' . $key;
			$entity->$setterName($spec);
		}
	}
	
	/**
	 * Fill Moto array corresponding to Moto entity structure, using the given array specs.
	 *
	 * Each key of the array specs must correspond to a Moto entity attribute.
	 *
	 * @param array $specs
	 */
	private function addSpecs(array $specs): void
	{
		foreach ($specs as $key => $spec) {
			$spec = array_key_exists($spec, $this->scrappedSuzuki['characteristics']) ? $this->scrappedSuzuki['characteristics'][$spec] : 0;
			if ($spec) $this->suzuki[$key] = $spec;
		}
	}
	
	/**
	 * Fill array Moto from data which need specifics treatments.
	 *
	 * To use it : create new function for each specific treatment and call it in this function.
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
		if ($cylinders) $this->suzuki['Cylinder'] = $cylinders;
	}
	
	private function addEngineCooling(string $engine): void
	{
		$engineCooling = match (1) {
			preg_match('#(refroidissement.liquide|refroidissement.par.eau)#i', $engine) => 'Liquide',
			preg_match('#refroidissement.par.ai#i', $engine) => 'Par air',
			default => 0
		};
		if ($engineCooling) $this->suzuki['EngineCooling'] = $engineCooling;
	}
	
	private function addPrice(): void
	{
		if (!preg_grep('#€#', $this->scrappedSuzuki['characteristics'])) {
			$this->suzuki['Price'] = 0;
			return;
		}
		foreach ($this->scrappedSuzuki['characteristics'] as $value) {
			if (str_contains($value, '€')) {
				$value = preg_replace('#[^0-9.]#', '', $value);
				$this->suzuki['Price'] = (int)$value;
				break;
			}
		}
	}
}
