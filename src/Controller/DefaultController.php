<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Goutte\Client;

class DefaultController extends AbstractController
{
	#[Route('/', name: 'test-suzuki-scrapping')]
	public function index()
	{
		$client = new Client();
		$crawler = $client->request('GET', 'https://www.suzuki-moto.com');
		$links = $crawler->filter('div.block-category.model > a')->each(function ($node) {
			$link = $node->attr('href');
			if (str_contains($link, '/scooters')) return;
			return $link;
		});

		$motos = array();
		foreach (array_filter($links) as $link) {
			$crawler = $client->request('GET', 'https://www.suzuki-moto.com/' . $link);
			$name = $crawler->filter('.essentiel.desktop')->text();
			$caracteristics = $crawler->filter('.tab-content.tab-content-1')->text();
			$moto = $name . ' - ' . $caracteristics;
			$motos[] = $moto;
		}
		dd($motos);
	}
}
