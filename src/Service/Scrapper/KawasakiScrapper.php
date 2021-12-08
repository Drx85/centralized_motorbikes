<?php

namespace App\Service\Scrapper;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;

class KawasakiScrapper
{
	private Client $client;
	private WebDriver $driver;
	private array $links = [];
	
	/**
	 * An associative array where the key is the li xpath hierarchy identifier of the corresponding category as value.
	 *
	 * @var array
	 */
	private array $categories = [
		2 => 'sporties',
		3 => 'roadsters',
		4 => 'vintages',
		5 => 'streets',
		6 => 'trails',
		7 => 'customs'
	];
	
	/**
	 * Scrap all Kawasaki motos specs on their website and return corresponding array.
	 *
	 * @return array
	 */
	public function getArray(): array
	{
		$this->client = Client::createChromeClient(__DIR__ . '/../../../drivers/chromedriver.exe');
		$this->client->request('GET', 'https://www.kawasaki.fr/fr/products');
		$this->driver = $this->client->getWebDriver();
		
		$this->client->manage()->timeouts()->implicitlyWait(10);
		$this->driver->findElement(WebDriverBy::id('aAgreeCookie'))->click();
		usleep(100000);
		$this->driver->findElement(WebDriverBy::className('knm-mobile__burger'))->click();
		usleep(100000);
		$this->driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/a/span/i'))->click();
		usleep(100000);
		$this->driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/a/span/i'))->click();
		usleep(100000);
		$this->driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[1]'))->click();
		
		$this->getLinks();
		$this->sanitizeLinks();
		
		foreach ($this->links as $link) {
			$name = $this->getName($link);
			$motos[$name]['type'] = $this->getType($link);
		}
		dd($motos);
	}
	
	/**
	 * Update links array scrapped with $i value which represents each category identifier in xpath.
	 *
	 * @return void
	 */
	private function getLinks(): void
	{
		for ($i = 2; $i <= 7; $i++) {
			usleep(100000);
			$motos = $this->driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[' . $i . ']'))->click();
			$motos = $motos->findElements(WebDriverBy::tagName('a'));
			
			foreach ($motos as $moto) {
				$this->links[] = $moto->getAttribute('href');
			}
		}
	}
	
	private function getName(string $link): string
	{
		preg_match("#^\/\/www.kawasaki.fr\/fr\/products\/[^\/]*\/[^\/]*\/([^\/]*)#", $link,$matches);
		return preg_replace('#_#', ' ', $matches[1]);
	}
	
	private function getType(string $link): string
	{
		return match (true) {
			str_contains($link, 'Trails') => 'trail',
			str_contains($link, 'Sportives') => 'sporty',
			str_contains($link, 'Roadsters') => 'roadster',
			str_contains($link, 'Z900') => 'vintage',
			str_contains($link, 'Routières') => 'street',
			str_contains($link, 'Customs') => 'custom',
			default => 'special',
		};
	}
	
	/**
	 * Delete empty and duplicated lines in the links array.
	 *
	 * @return void
	 */
	private function sanitizeLinks(): void
	{
		$this->links = array_unique($this->links);
		foreach ($this->links as $key => $link) {
			if (!preg_match('#^\/\/www.kawasaki.fr\/fr\/products\/[^\/]*\/#', $link)) unset($this->links[$key]);
		}
		$this->links = array_filter($this->links);
	}
}