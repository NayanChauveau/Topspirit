<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Platform;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        // $this->user();
        $this->platform($manager);

        $manager->flush();
    }

    private function user()
    {
        $user = new User;
        $user
            ->setEmail('nayan.chauveau@gmail.com')
            ->setPassword($this->hasher->hashPassword($user, 'testtest'))
        ;
        $this->manager->persist($user);
    }

    private function platform($manager)
    {
        $user = $this->userRepo->find(9); //Trouver comment prendre un user au hasard dans le findAll.

        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $platform = new Platform;
            $platform
                ->setName($faker->words(3, true))
                ->setUrl($faker->url())
                ->setBanner('img-template.jpg')
                ->setContent($faker->sentence(15))
                ->setUser($user)
                ->setCreatedAt(new \DateTimeImmutable())
            ;
            $manager->persist($platform);
        }
    }
}
