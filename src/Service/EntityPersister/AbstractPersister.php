<?php

namespace App\Service\EntityPersister;

use App\Entity\Moto;

abstract class AbstractPersister
{
	protected array $scrappedMotos;
	protected mixed $scrappedMoto;
	protected array $moto;
	protected Moto  $oldMoto;
	
	protected abstract function addSpecsNeedTreatment();
	public abstract function updateFromScrappedData();
	
	/**
	 * Fill Moto array corresponding to Moto entity structure, using the given array specs.
	 *
	 * Each key of the array specs must correspond to a Moto entity attribute.
	 *
	 * @param array $specs
	 */
	protected function addSpecs(array $specs): void
	{
		foreach ($specs as $key => $spec) {
			$spec = array_key_exists($spec, $this->scrappedMoto['characteristics']) ? $this->scrappedMoto['characteristics'][$spec] : 0;
			if ($spec) $this->moto[$key] = $spec;
		}
	}
	
	/**
	 * Fill Moto entity of this class, using the given Moto entity.
	 *
	 * @param Moto $entity
	 */
	protected function fillEntity(Moto $entity): void
	{
		foreach ($this->moto as $key => $spec) {
			$setterName = 'set' . $key;
			$entity->setPrice(1);
			$entity->$setterName($spec);
		}
	}
	
	protected function updateOrCreateMoto(): void
	{
		$oldMoto = $this->motoRepository->findOneBy(array('name' => $this->moto['Name']));
		
		if ($oldMoto instanceof Moto) {
			$this->fillEntity($oldMoto);
		} else {
			$newSuzuki = new Moto();
			$this->fillEntity($newSuzuki);
			$this->manager->persist($newSuzuki);
		}
	}
}