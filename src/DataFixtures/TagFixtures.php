<?php
namespace App\DataFixtures;

use App\Entity\tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

Class TagFixtures extends Fixture
{
	const TAGS = [
		'Benchmark',
		'Languages',
		'Memes',
		'Why not?',
		'Trolls'
	];

	public function load(ObjectManager $manager)
	{
		foreach (self::TAGS as $key => $tagName) {
			$tag = new Tag();
			$tag->setName($tagName);
			$manager->persist($tag);
			$this->addReference('tag_' . $key, $tag);
		} 
		$manager->flush();
	}
}
