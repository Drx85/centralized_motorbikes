<?php

namespace App\Service\EntityPersister;

use App\Repository\BrandRepository;
use App\Repository\MotoRepository;
use App\Repository\TypeRepository;
use App\Service\Scrapper\KawasakiScrapper;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;

final class KawasakiPersister extends AbstractPersister
{
	protected array $specs = [
		'EngineDistribution' => 'Type de moteur',
		'VolumetricRatio'    => 'Taux de compression',
		'HorsePower'         => 'Puissance maximale',
		'FuelSystem'         => 'Système d\'alimentation',
		'FuelConsumption'    => 'Consommation de carburant',
		'MaxTorque'          => 'Couple maximal',
		'GearNumber'         => 'Transmission',
		'Clutch'             => 'Embrayage',
		'Frame'              => 'Type de cadre',
		'CasterAngle'        => 'Angle de direction G/D',
		'CasterTrail'        => 'Chasse',
		'Wheelbase'          => 'Empattement',
		'FrontSuspension'    => 'Suspension, avant',
		'RearSuspension'     => 'Suspension, arrière',
		'FrontBrake'         => 'Freins, avant',
		'RearBrake'          => 'Freins, arrière',
		'FrontTire'          => 'Pneu, avant',
		'BackTire'           => 'Pneu, arrière',
		'LxlxH'              => 'L x l x H',
		'SeatHeight'         => 'Hauteur de selle',
		'GroundClearance'    => 'Garde au sol',
		'FuelCapacity'       => 'Capacité de carburant',
		'Weight'             => 'Poids tous pleins faits',
		'FinalDrive' 		 => 'Entraînement final'
	];
	
	public function __construct(
		protected KawasakiScrapper       $kawasakiScrapper,
		protected EntityManagerInterface $manager,
		protected MotoRepository         $motoRepository,
		protected BrandRepository        $brandRepository,
		protected TypeRepository         $typeRepository
	)
	{
		$this->scrappedMotos = $kawasakiScrapper->getArray();
	}
	
	#[NoReturn] public function updateFromScrappedData()
	{
		foreach ($this->scrappedMotos as $key => $this->scrappedMoto) {
			$this->moto = [
				'Name'  => $key,
				'Brand' => $this->brandRepository->findOneBy(array('name' => 'Kawasaki')),
				'Type'  => $this->typeRepository->findOneBy(array('name' => $this->scrappedMoto['type']))
			];
			
			$this->addSpecsNeedTreatment();
			$this->addSpecs($this->specs);
			$this->updateOrCreateMoto();
		}
		$this->manager->flush();
		dd($this->scrappedMotos);
	}
	
	protected function addSpecsNeedTreatment()
	{
		$engine = array_key_exists('Type de moteur', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Type de moteur'] : '';
		$this->addCylinders($engine);
		$this->addEngineCooling($engine);
		$this->addStarter();
		$this->addFuelConsumption();
	}
	
	private function addCylinders(string $engine): void
	{
		$cylinders = match (1) {
			preg_match('#(bicylindre|2.cylindre)#i', $engine) => 2,
			preg_match('#4.cylindre|Quatre.cylindre#i', $engine) => 4,
			preg_match('#monocylindre#i', $engine) => 1,
			default => 0
		};
		if ($cylinders) $this->moto['Cylinder'] = $cylinders;
	}
	
	private function addEngineCooling(string $engine): void
	{
		$engineCooling = match (1) {
			preg_match('#(refroidissement.liquide|refroidissement.par.eau)#i', $engine) => 'Liquide',
			preg_match('#refroidissement.par.air#i', $engine) => 'Par air',
			default => 0
		};
		if ($engineCooling) $this->moto['EngineCooling'] = $engineCooling;
	}
	
	private function addStarter()
	{
		$starter = array_key_exists('Allumage', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Allumage'] : '';
		if (!$starter) $starter = array_key_exists('Système de démarrage', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Système de démarrage'] : '';
		if ($starter) $this->moto['Starter'] = $starter;
	}
	
	private function addFuelConsumption()
	{
		$fuelConsumption = array_key_exists('Consommation de carburant', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Consommation de carburant'] : '';
		if (!$fuelConsumption) $fuelConsumption = array_key_exists('Consommation de carburant avec le kit 35 kW', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Consommation de carburant avec le kit 35 kW'] : '';
		if ($fuelConsumption) $this->moto['FuelConsumption'] = $fuelConsumption;
	}
}