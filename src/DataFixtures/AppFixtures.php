<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstNameFemale);

            $user->setUsername($user->getPrenom().$faker->biasedNumberBetween($min = 10, $max = 20, $function = 'sqrt'));
            $user->setEmail($user->getPrenom().".".$user->getNom()."@".$faker->freeEmailDomain);
            $user->setTelephone($faker->e164PhoneNumber);
            $user->setPhoto('/img.pp.png');
            $user->setActif(true);
            $user->setRole('ROLE_USER');

            $passwordEncode = $this->encoder->encodePassword($user, $user->getPrenom());

            $user->setPassword($passwordEncode);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
