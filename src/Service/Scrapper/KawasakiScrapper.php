<?php

namespace App\Service\Scrapper;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;

class KawasakiScrapper extends AbstractScrapper
{
	private WebDriver $driver;
	private array $links = [];
	
	/**
	 * Scrap all Kawasaki motos specs on their website and return corresponding array.
	 *
	 * @return array
	 */
	public function getArray(): array
	{
		$pantherClient = Client::createChromeClient(__DIR__ . '/../../../drivers/chromedriver.exe');
		$pantherClient->request('GET', 'https://www.kawasaki.fr/fr/products');
		$this->driver = $pantherClient->getWebDriver();
		
		$pantherClient->manage()->timeouts()->implicitlyWait(10);
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
		
		$goutteClient = new \Goutte\Client();
	
		
		foreach ($this->links as $link) {
			$link = preg_replace('#overview#', 'specifications', $link);
			$name = $this->getName($link);
			$motos[$name]['type'] = $this->getType($link);
			
			$crawler = $goutteClient->request('GET', $link);
			
			$characteristicNames[$name] = $crawler->filter('dt')->each(function ($node) {
				return $node->text();
			});
			
			$motos[$name]['characteristics'] = $crawler->filter('dd')->each(function ($node) {
				return $node->text();
			});
		}
		$a = $this->mergeArrays($motos, $characteristicNames);
		dd($a);
	}
	
	/**
	 * Update links array scrapped with $i value which represents each category identifier in xpath.
	 *
	 * @return void
	 */
	private function getLinks(): void
	{
		for ($i = 2; $i <= 7; $i++) {
			usleep(200000);
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
	 * Delete empty, duplicated and irrelevant links in the corresponding array.
	 *
	 * @return void
	 */
	private function sanitizeLinks(): void
	{
		$this->links = array_unique($this->links);
		$this->links = array_filter($this->links, 'self::removeIrrelevant');
	}
	
	private static function removeIrrelevant($var): bool
	{
		if (!$var) return false;
		if (!preg_match('#^\/\/www.kawasaki.fr\/fr\/products\/[^\/]*\/#', $var)) return false;
		return true;
	}
}
