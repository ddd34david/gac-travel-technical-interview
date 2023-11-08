<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private HttpClientInterface $client)
    {
    }
    
    public function load(ObjectManager $manager): void
    {
        $response = $this->client->request(
            'GET',
            'https://fakestoreapi.com/products'
        );

        $products = $response->toArray();
        
        foreach($products as $fakeProducts) {
            $product = new Product();
            $product->setName(explode(' ',trim($fakeProducts['title']))[0]);
            $product->setCategory($this->getReference($fakeProducts['category']));

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
