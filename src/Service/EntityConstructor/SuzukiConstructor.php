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
	private $scrappedSuzuki;
	private $suzuki;
	
	public function __construct(SuzukiScrapper         $scrappedSuzukies,
								EntityManagerInterface $manager,
								MotoRepository         $motoRepository,
								BrandRepository        $brandRepository,
								TypeRepository         $typeRepository
	)
	{
		$scrappedSuzukies = $scrappedSuzukies->getArray();
		
//		$oldMotos = $motoRepository->findAll();
		
		foreach ($scrappedSuzukies as $key => $scrappedSuzuki) {
			$this->suzuki = new Moto();
			$this->scrappedSuzuki = $scrappedSuzuki;
			
			$this->suzuki->setBrand($brandRepository->findOneBy(array('name' => 'Suzuki')));
			$this->suzuki->setType($typeRepository->findOneBy(array('name' => 'offroad')));
			$this->suzuki->setName($key);
			
			$engine = array_key_exists('Type :', $scrappedSuzuki['characteristics']) ? $scrappedSuzuki['characteristics']['Type :'] : '';
			
			$cylinders = match (1) {
				preg_match('#(twin|bicylindre|2.cylindre)#i', $engine) => 2,
				preg_match('#4.cylindre#i', $engine) => 4,
				preg_match('#monocylindre#i', $engine) => 1,
				default => 0
			};
			if ($cylinders) $this->suzuki->setCylinder($cylinders);
			
			$engineCooling = match (1) {
				preg_match('#(refroidissement.liquide|refroidissement.par.eau)#i', $engine) => 'Liquide',
				preg_match('#refroidissement.par.ai#i', $engine) => 'Par air',
				default => 0
			};
			if ($engineCooling) $this->suzuki->setEngineCooling($engineCooling);
			
			
			foreach ($scrappedSuzuki['characteristics'] as $value) {
				if (str_contains($value, '€')) {
					$value = preg_replace('#[^0-9.]#', '', $value);
					$this->suzuki->setPrice((int) $value);
					break;
				}
			}
			
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
			
			$manager->persist($this->suzuki);
		}
		
		$manager->flush();
		dd($scrappedSuzukies);
	}
	
	private function addSpecs(array $specs)
	{
		foreach ($specs as $key => $spec) {
			$setterName = 'set' . $key;
			$spec = array_key_exists($spec, $this->scrappedSuzuki['characteristics']) ? $this->scrappedSuzuki['characteristics'][$spec] : 0;
			if ($spec) $this->suzuki->$setterName($spec);
		}
	}
	
/*	private function saveNewMoto()
	{
	
	}
	
	private function editOldMoto()
	{
	
	}*/
}