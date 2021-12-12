<?php

namespace App\Service\Scrapper;

Abstract class AbstractScrapper
{
	/**
	 * Add to the array which contains motos characteristics the name of these characteristics, thanks to another array.
	 *
	 * @param array $motos
	 * @param array $characteristicNames
	 *
	 * @return array
	 */
	protected function mergeArrays(array $motos, array $characteristicNames): array
	{
		foreach ($motos as $key => $moto) {
			$motos[$key]['characteristics'] = array_combine($characteristicNames[$key], $moto['characteristics']);
		}
		
		return $motos;
	}
	
	protected abstract function getArray();
	protected abstract function getLinks();
	protected abstract function getType(string $links);
}
