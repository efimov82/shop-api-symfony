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
      0 => ['id' => 1, 'name' => 'Tomato', 'description' => 'Tomato from Asia', 'image' => 'tomato.webp'],
      1 => ['id' => 2, 'name' => 'Banana', 'description' => 'Banana from Ecvador', 'image' => 'banana.webp'],
      2 => ['id' => 3, 'name' => 'Onion', 'description' => 'Onion from Japan', 'image' => 'onion.webp'],
      4 => ['id' => 4, 'name' => 'Apple', 'description' => 'Apple from China', 'image' => 'apple.webp'],
      5 => ['id' => 5, 'name' => 'Broccoli', 'description' => 'Broccoli from China', 'image' => 'broccoli.webp'],
      6 => ['id' => 6, 'name' => 'Cabbage', 'description' => 'Broccoli from Russia', 'image' => 'cabbage.webp'],
      7 => ['id' => 7, 'name' => 'Carrots', 'description' => 'Carrots from Belarus', 'image' => 'carrots.webp'],
      8 => ['id' => 8, 'name' => 'Cucumbers', 'description' => 'Cucumbers from Belarus', 'image' => 'cucumbers.webp'],
      9 => ['id' => 9, 'name' => 'Kiwi', 'description' => 'Kiwi from Africa', 'image' => 'kiwi.webp'],
      10 => ['id' => 10, 'name' => 'Lemon', 'description' => 'Lemon from Africa', 'image' => 'lemon.webp'],
      11 => ['id' => 11, 'name' => 'Lime', 'description' => 'Lime from Africa', 'image' => 'lime.webp'],
      12 => ['id' => 12, 'name' => 'Melon', 'description' => 'Melon from Japan', 'image' => 'melon.webp'],
      13 => ['id' => 13, 'name' => 'Nectarine', 'description' => 'Nectarine from Japan', 'image' => 'nectarine.webp'],
      14 => ['id' => 14, 'name' => 'Peach', 'description' => 'Peach from Japan', 'image' => 'peach.webp'],
    ];

    foreach ($products as $id => $product) {
      $this->createProduct($manager, $product['id'], $product['name'], $product['description'], $product['image']);
    }

    // Default user
    $user = new User();
    $user->setFirstName('User');
    $user->setLastName('Example');
    $user->setEmail('user@example.com');

    // use: php bin/console security:hash-password
    // password: user
    $user->setPassword('$2y$13$HynG6HO55eFSXOfSNhH3cuhZ5NtJ/srnEW6lJbDZjLGtgSis8Jqbm');
    $user->setRoles(["ROLE_USER"]);
    $manager->persist($user);

    // Admin
    $admin = new User();
    $admin->setFirstName('Admin');
    $admin->setLastName('Admin');
    $admin->setEmail('admin@example.com');

    // password: admin
    $admin->setPassword('$2y$13$QE3JbOUz6CYvKVwWxmo7J.BGVgxPeI6zXHtWnObTl3DmvEz9USYYe');
    $admin->setRoles(["ROLE_ADMIN"]);
    $manager->persist($admin);

    // Manager
    $m = new User();
    $m->setFirstName('Manager');
    $m->setLastName('Manager');
    $m->setEmail('manager@example.com');

    // password: manager
    $m->setPassword('$2y$13$gFWDQFGI9pFlDzYQBQqDceYH2.x8d/DzxYtIQvUXDpaGw81Nixz5y');
    $m->setRoles(["ROLE_MANAGER"]);
    $manager->persist($m);


    $manager->flush();
  }


  private function createProduct(
    ObjectManager $manager,
    int $id,
    string $name,
    string $description,
    string $image
  ) {
    $product = new Product();

    $product->setId($id);
    $product->setName($name);
    $product->setDescription($description);
    $product->setImage($image);
    $product->setPrice(round(mt_rand(2, 40) / mt_rand(1, 10), 2));
    $manager->persist($product);
  }
}
