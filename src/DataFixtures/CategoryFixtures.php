<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryFixtures extends Fixture
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $response = $this->client->request(
            'GET',
            'https://fakestoreapi.com/products/categories'
        );

        $categories = $response->toArray();

        foreach($categories as $fakeCategory) {
            $category = new Category();
            $category->setName($fakeCategory);
            $manager->persist($category);

            $this->addReference($fakeCategory, $category);
        }

        $manager->flush();

        
    }
}
