<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = array('Suzuki', 'Yamaha', 'Honda', 'BMW', 'Kawasaki');
		foreach ($brands as $name) {
			$brand = new Brand();
			$brand->setName($name);
			$manager->persist($brand);
		}
		$manager->flush();
    }
}
