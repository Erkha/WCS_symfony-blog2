<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{		
	private $passwordEncoder;

  public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
      $this->passwordEncoder = $passwordEncoder;
    }

  public function load(ObjectManager $manager)
  {
      // Création d’un utilisateur de type “auteur”
      $author = new User();
      $author->setEmail('author1@monsite.com');
      $author->setRoles(['ROLE_AUTHOR']);
      $author->setPassword($this->passwordEncoder->encodePassword(
          $author,
          'authorpassword1'
      ));
      $manager->persist($author);
      $this->addReference('author', $author);

      $author = new User();
      $author->setEmail('author2@monsite.com');
      $author->setRoles(['ROLE_AUTHOR']);
      $author->setPassword($this->passwordEncoder->encodePassword(
          $author,
          'authorpassword2'
      ));
      $manager->persist($author);

      // Création d’un utilisateur de type “administrateur”
      $admin = new User();
      $admin->setEmail('admin@monsite.com');
      $admin->setRoles(['ROLE_ADMIN']);
      $admin->setPassword($this->passwordEncoder->encodePassword(
          $admin,
          'adminpassword'
      ));

      $manager->persist($admin);

      // Sauvegarde des 2 nouveaux utilisateurs :
      $manager->flush();
  }
}
