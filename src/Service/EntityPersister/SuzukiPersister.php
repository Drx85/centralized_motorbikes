<?php

namespace App\Service\EntityPersister;

use App\Repository\BrandRepository;
use App\Repository\MotoRepository;
use App\Repository\TypeRepository;
use App\Service\Scrapper\SuzukiScrapper;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;

final class SuzukiPersister extends AbstractPersister
{
	protected array $specs = [
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
	];
	
	public function __construct(
		protected SuzukiScrapper 		 $suzukiScrapper,
		protected EntityManagerInterface $manager,
		protected MotoRepository         $motoRepository,
		protected BrandRepository        $brandRepository,
		protected TypeRepository         $typeRepository
	)
	{
		$this->scrappedMotos = $suzukiScrapper->getArray();
	}
	
	#[NoReturn] public function updateFromScrappedData()
	{
		foreach ($this->scrappedMotos as $key => $this->scrappedMoto) {
			$this->moto = [
				'Name'  => $key,
				'Brand' => $this->brandRepository->findOneBy(array('name' => 'Suzuki')),
				'Type'  => $this->typeRepository->findOneBy(array('name' => $this->scrappedMoto['type']))
			];
			
			$this->addSpecsNeedTreatment();
			$this->addSpecs($this->specs);
			$this->updateOrCreateMoto();
		}
		$this->manager->flush();
		dd($this->scrappedMotos);
	}
	
	/**
	 * Fill array Moto from data which need specifics treatments.
	 *
	 * To use it : create new function for each specific treatment and call it in this function.
	 *
	 */
	protected function addSpecsNeedTreatment(): void
	{
		$engine = array_key_exists('Type :', $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics']['Type :'] : '';
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
		if ($cylinders) $this->moto['Cylinder'] = $cylinders;
	}
	
	private function addEngineCooling(string $engine): void
	{
		$engineCooling = match (1) {
			preg_match('#(refroidissement.liquide|refroidissement.par.eau)#i', $engine) => 'Liquide',
			preg_match('#refroidissement.par.ai#i', $engine) => 'Par air',
			default => 0
		};
		if ($engineCooling) $this->moto['EngineCooling'] = $engineCooling;
	}
	
	private function addPrice(): void
	{
		if (!preg_grep('#€#', $this->scrappedMoto['characteristics'])) {
			$this->moto['Price'] = 0;
			return;
		}
		foreach ($this->scrappedMoto['characteristics'] as $value) {
			if (str_contains($value, '€')) {
				$value = preg_replace('#[^0-9.]#', '', $value);
				$this->moto['Price'] = (int)$value;
				break;
			}
		}
	}
}
