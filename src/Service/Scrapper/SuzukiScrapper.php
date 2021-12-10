<?php

namespace App\Service\Scrapper;

use Goutte\Client;

class SuzukiScrapper extends AbstractScrapper
{
	private Client $client;
	
	/**
	 * Scrap all Suzuki motos specs on their website and return corresponding array.
	 *
	 * @return array
	 */
	public function getArray(): array
	{
		$this->client = new Client();
		$categoryLinks = $this->getLinks();
		foreach (array_filter($categoryLinks) as $link) {
			$crawler = $this->client->request('GET', 'https://www.suzuki-moto.com/' . $link);
			$name = $crawler->filter('.essentiel.desktop')->text();
			$motos[$name]['type'] = $this->getType($link);
			
			$characteristicNames[$name] = $crawler->filter('.tab-content.tab-content-1 .left')->each(function ($node) {
				return $node->text();
			});
			
			$motos[$name]['characteristics'] = $crawler->filter('.tab-content.tab-content-1 .right')->each(function ($node) {
				return $node->text();
			});
		}
		
		dd($this->mergeArrays($motos, $characteristicNames));
	}
	
	/**
	 * Return an array of moto Category links to be scrapped.
	 *
	 * @return array
	 */
	private function getLinks(): array
	{
		$crawler = $this->client->request('GET', 'https://www.suzuki-moto.com');
		
		return $crawler->filter('div.block-category.model > a')->each(function ($node) {
			$link = $node->attr('href');
			if (str_contains($link, '/scooters')) return;
			return $link;
		});
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
}
