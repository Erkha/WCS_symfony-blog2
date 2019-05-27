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
			$manager->persist($article);
		  $article->setCategory($this->getReference('categorie_'.(($i+1)%5)));
			$manager->flush($article);
		}
	}

	public function getDependencies()
 	{
 	return [CategoryFixtures::class];
 	}
}