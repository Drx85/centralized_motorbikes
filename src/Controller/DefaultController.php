<?php

namespace App\Controller;

use App\Service\EntityConstructor\SuzukiConstructor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
	#[Route('/', name: 'test-suzuki-scrapping')]
	public function index(SuzukiConstructor $suzuki)
	{
	
	}
}
