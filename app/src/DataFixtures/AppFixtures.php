<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $products = [
      0 => ['id' => 1, 'name' => 'Tomato', 'description' => 'Tomato from Asia'],
      1 => ['id' => 2, 'name' => 'Potato', 'description' => 'Potato from Belarus'],
      2 => ['id' => 3, 'name' => 'Onion', 'description' => 'Onion from Japan'],
      4 => ['id' => 4, 'name' => 'Apple', 'description' => 'Apple from China']
    ];

    foreach ($products as $id => $product) {
      $this->createProduct($manager, $product['id'], $product['name'], $product['description']);
    }

    // Default user
    $user = new User();
    $user->setFirstName('User');
    $user->setLastName('Example');
    $user->setEmail('user@example.com');

    // use: php bin/console security:hash-password
    // password: user
    $user->setPassword('$2y$13$HynG6HO55eFSXOfSNhH3cuhZ5NtJ/srnEW6lJbDZjLGtgSis8Jqbm');
    $user->setRoles(["ROLE_ADMIN", "ROLE_USER", "ROLE_MANAGER"]);
    $manager->persist($user);

    $manager->flush();
  }


  private function createProduct(ObjectManager $manager, int $id, string $name, string $description)
  {
    $product = new Product();

    $product->setId($id);
    $product->setName($name);
    $product->setDescription($description);
    $product->setPrice(mt_rand(5, 50) / mt_rand(1, 10));
    $manager->persist($product);
  }
}
