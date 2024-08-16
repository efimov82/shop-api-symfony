<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $products = [
      0 => ['name' => 'Tomato', 'description' => 'Tomato from Asia'],
      1 => ['name' => 'Potato', 'description' => 'Potato from Belarus'],
      2 => ['name' => 'Onion', 'description' => 'Onion from Japan'],
      4 => ['name' => 'Apple', 'description' => 'Apple from China']
    ];

    foreach ($products as $id => $product) {
      $this->createProduct($manager, $product['name'], $product['description']);
    }
    // $product = new Product();
    // $product->setName('Tomato');
    // $product->setDescription('Tomato from Asia');
    // $product->setPrice(mt_rand(5, 50) / mt_rand(1, 10));
    // $manager->persist($product);

    // $product2 = new Product();
    // $product2->setName('Potato');
    // $product->setDescription('Fresh potato from Belarus');
    // $product2->setPrice(round(mt_rand(5, 50) / mt_rand(1, 10), 2));
    // $manager->persist($product2);

    // $product3 = new Product();
    // $product3->setName('Onion');
    // $product->setDescription('Fresh onion from Belarus');
    // $product3->setPrice(mt_rand(5, 50) / mt_rand(1, 10));
    // $manager->persist($product3);

    // $product4 = new Product();
    // $product4->setName('Apple');
    // $product->setDescription('Apple from Bulgaria');
    // $product4->setPrice(mt_rand(5, 50) / mt_rand(1, 10));
    // $manager->persist($product4);


    $manager->flush();
  }


  private function createProduct(ObjectManager $manager, string $name, string $description)
  {
    $product = new Product();

    $product->setName($name);
    $product->setDescription($description);
    $product->setPrice(mt_rand(5, 50) / mt_rand(1, 10));
    $manager->persist($product);

    // return $product;
  }
}
