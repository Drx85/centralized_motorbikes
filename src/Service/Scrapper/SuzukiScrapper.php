<?php

namespace App\Service\Scrapper;

use Goutte\Client;

class SuzukiScrapper
{
	/**
	 * Scrap all Suzuki motos specs on their website and return corresponding array.
	 *
	 * @return array
	 */
	public function getArray(): array
	{
		$client = new Client();
		$crawler = $client->request('GET', 'https://www.suzuki-moto.com');
		$links = $crawler->filter('div.block-category.model > a')->each(function ($node) {
			$link = $node->attr('href');
			if (str_contains($link, '/scooters')) return;
			return $link;
		});
		
		foreach (array_filter($links) as $link) {
			$crawler = $client->request('GET', 'https://www.suzuki-moto.com/' . $link);
			$name = $crawler->filter('.essentiel.desktop')->text();
			$motos[$name]['type'] = $this->getType($link);
			
			$characteristicNames[$name] = $crawler->filter('.tab-content.tab-content-1 .left')->each(function ($node) {
				return $node->text();
			});
			
			$motos[$name]['characteristics'] = $crawler->filter('.tab-content.tab-content-1 .right')->each(function ($node) {
				return $node->text();
			});
		}
		return $this->mergeArrays($motos, $characteristicNames);
	}
	
	/**
	 * Return the type of moto, depending on what contains the given URL.
	 *
	 * 'special' is default value.
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	private function getType(string $link): string
	{
		return match (true) {
			str_contains($link, 'trails-routiers') => 'trail',
			str_contains($link, 'sportives') => 'sporty',
			str_contains($link, 'roadsters') => 'roadster',
			str_contains($link, 'motos-125-cm3') => '125',
			str_contains($link, 'tout-terrain') => 'offroad',
			default => 'special',
		};
	}
	
	/**
	 * Add to the array which contains moto characteristics the name of these characteristics, thanks to another array.
	 *
	 * @param array $motos
	 * @param array $characteristicNames
	 *
	 * @return array
	 */
	private function mergeArrays(array $motos, array $characteristicNames): array
	{
		$keys = array_keys($motos);
		foreach ($motos as $moto) {
			foreach ($moto['characteristics'] as $ignored) {
				$c = 0;
				foreach ($keys as $value) {
					$motos[$keys[$c]]['characteristics'] = array_combine($characteristicNames[$value], $motos[$keys[$c]]['characteristics']);
					$c++;
				}
			}
		}
		return $motos;
	}
}