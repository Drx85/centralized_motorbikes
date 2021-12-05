<?php

namespace App\Service\Scrapper;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;

class KawasakiScrapper
{
	private Client $client;
	
	/**
	 * Scrap all Kawasaki motos specs on their website and return corresponding array.
	 *
	 * @return array
	 */
	public function getArray(): array
	{
		$this->client = Client::createChromeClient(__DIR__ . '/../../../drivers/chromedriver.exe');
		$this->client->request('GET', 'https://www.kawasaki.fr/fr/products');
		$driver = $this->client->getWebDriver();
		
		$this->client->manage()->timeouts()->implicitlyWait(10);
		$driver->findElement(WebDriverBy::id('aAgreeCookie'))->click();
		usleep(100000);
		$driver->findElement(WebDriverBy::className('knm-mobile__burger'))->click();
		usleep(100000);
		$driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/a/span/i'))->click();
		usleep(100000);
		$driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/a/span/i'))->click();
		usleep(100000);
		$driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[1]'))->click();
		
		$links = [];
		
		usleep(100000);
		$hypersporties = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[1]/div/div/div'));
		$hypersporties = $hypersporties->findElements(WebDriverBy::tagName('a'));
		
		foreach ($hypersporties as $link)
		{
			$links[] = $link->getAttribute('href');
		}
		
		usleep(100000);
		$sporties = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[2]'))->click();
		$sporties = $sporties->findElements(WebDriverBy::tagName('a'));
		
		foreach ($sporties as $sporty) {
			$links[] = $sporty->getAttribute('href');
		}
		
		usleep(100000);
		$roadsters = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[3]'))->click();
		$roadsters = $roadsters->findElements(WebDriverBy::tagName('a'));
		
		foreach ($roadsters as $roadster) {
			$links[] = $roadster->getAttribute('href');
		}
		
		usleep(100000);
		$vintages = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[4]'))->click();
		$vintages = $vintages->findElements(WebDriverBy::tagName('a'));
		
		foreach ($vintages as $vintage) {
			$links[] = $vintage->getAttribute('href');
		}
		
		usleep(100000);
		$streets = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[5]'))->click();
		$streets = $streets->findElements(WebDriverBy::tagName('a'));
		
		foreach ($streets as $street) {
			$links[] = $street->getAttribute('href');
		}
		
		usleep(100000);
		$trails = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[6]'))->click();
		$trails = $trails->findElements(WebDriverBy::tagName('a'));
		
		foreach ($trails as $trail) {
			$links[] = $trail->getAttribute('href');
		}
		
		usleep(100000);
		$customs = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[7]'))->click();
		$customs = $customs->findElements(WebDriverBy::tagName('a'));
		
		foreach ($customs as $custom) {
			$links[] = $custom->getAttribute('href');
		}
		
		usleep(100000);
		$a2s = $driver->findElement(WebDriverBy::xpath('/html/body/nav/div/ul/li[1]/ul/li[1]/ul/li[8]'))->click();
		$a2s = $a2s->findElements(WebDriverBy::tagName('a'));
		
		foreach ($a2s as $a2) {
			$links[] = $a2->getAttribute('href');
		}

		dd($links);
	}
}