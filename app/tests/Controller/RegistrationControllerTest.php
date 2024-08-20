<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Client;

class RegistrationController extends WebTestCase
{
  // private string $apiHost = '';
  private string $apiUrl = '/api/v1/registration';

  protected function setUp(): void
  {
    // $this->apiHost = $_SERVER['REST_API_HOST'];
    // $this->apiUrl = "/registration";

    // var_dump($_SERVER['REST_API_HOST']);
    // var_dump($_SERVER['APP_ENV']);
    // var_dump($_SERVER['DATABASE_URL']);
  }

  public function testRegistrationWithEmptyData(): void
  {
    $client = static::createClient();

    $client->request(
      'POST',
      $this->apiUrl,
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      ''
    );
    // $response = $client->getResponse()->getContent();

    // $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(422);
  }

  public function testRegistrationWithIncompleteData(): void
  {
    $client = static::createClient();

    $data = [
      'first_name' => 'John',
      'last_name' => 'John',
      'email' => 'test@example.com'
    ];

    $client->request(
      'POST',
      $this->apiUrl, 
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      json_encode($data)
    );

    $response = $client->getResponse()->getContent();

    $this->assertResponseStatusCodeSame(422);
    $this->assertEquals('{"error":"This value should be of type string."}', $response);
  }

  public function testRegistrationWithExistingEmail(): void
  {
    $client = static::createClient();

    $data = [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'email' => 'test@example.com',
      'password' => 'test123'
    ];

    $client->request(
      'POST', 
      $this->apiUrl,
      [], 
      [], 
      ['CONTENT_TYPE' => 'application/json'],
      json_encode($data)
    );

    $response = $client->getResponse()->getContent();

    $this->assertResponseStatusCodeSame(409);
    $this->assertEquals('{"error":"userAlreadyExists"}', $response);
  }

  public function testSuccessRegistration(): void
  {
    $client = static::createClient();

    $data = [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'email' => 'test2@example.com',
      'password' => 'test123'
    ];

    $client->request(
      'POST', 
      $this->apiUrl,
      [], 
      [], 
      ['CONTENT_TYPE' => 'application/json'],
      json_encode($data)
    );

    $response = $client->getResponse()->getContent();

    $this->assertResponseStatusCodeSame(201);
    $this->assertEquals('{"error":"userAlreadyExists"}', $response);
  }

}
