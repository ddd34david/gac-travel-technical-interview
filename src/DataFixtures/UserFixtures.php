<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    
    public function __construct(private HttpClientInterface $client, UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $response = $this->client->request(
            'GET',
            'https://fakestoreapi.com/users'
        );

        $users = $response->toArray();
        
        foreach($users as $fakeUser) {
            $user = new User();
            $user->setUsername($fakeUser['username']);
            $user->setPassword(
                $this->hasher->hashPassword($user,$fakeUser['password'])
            );
            $user->setActive(true);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
