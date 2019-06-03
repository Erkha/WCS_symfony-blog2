<?php
namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

Class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		for ($i=0; $i < 50; $i++) { 
			$article = new Article();
			$faker  =  Faker\Factory::create('fr_FR');
			$article->setContent($faker->paragraph($nbSentences = 3, $variableNbSentences = true));
			$article->setTitle($faker->sentence($nbWords = 4, $variableNbWords = true));
			$article->setSlug();
			$article->setAuthor($this->getReference('author'));
		  $article->setCategory($this->getReference('categorie_'.rand(0,4)));
			for ($j=0; $j < rand(0,4); $j++) { 
				$article->addTag($this->getReference('tag_'.rand(0,4)));
			}
			$manager->persist($article);
		}
		$manager->flush();
	}

	public function getDependencies()
 	{
	 	return [
	 		UserFixtures::class,
	 		CategoryFixtures::class,
			TagFixtures::class
		];
 	}
}