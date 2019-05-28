<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

Class CategoryFixtures extends Fixture
{
	const CATEGORIES = [
		'PHP',
		'Java',
		'Javascript',
		'Ruby',
		'DevOps'
	];

	public function load(ObjectManager $manager)
	{
		foreach (self::CATEGORIES as $key => $categoryName) {
			$category = new Category();
			$category->setName($categoryName);
			$manager->persist($category);
			$this->addReference('categorie_' . $key, $category);
		} 
		$manager->flush();
	}
}
