<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class RegistrationControllerTest extends WebTestCase
{
  // private string $apiHost = '';
  private string $apiUrl = '/api/v1/registration';

  /**
   * var \Doctrine\ORM\EntityManager
   */
  private $entityManager;
  private $dbConnection;


  // public function __construct(?string $name = null, array $data = [], $dataName = '')
  // {
  //   parent::__construct($name, $data, $dataName);

  // $kernel = self::bootKernel();

  // $this->entityManager = $kernel->getContainer()
  //   ->get('doctrine')
  //   ->getManager();

  // $this->dbConnection = $this->entityManager->getConnection();
  // }

  // public function __construct(?string $name = null, array $data = [], $dataName = '')
  // {    
  //   parent::__construct($name, $data, $dataName);
  //   $kernel = self::bootKernel();

  // }

  protected static $application;

  protected function setUp(): void
  {
    // static::getClient()->disableReboot();

    // $this->entityManager = static::getContainer()->get('doctrine')->getManager();

    // $this->dbConnection->executeQuery('TRUNCATE TABLE user');

    // $this->entityManager->remove();

    // $this->runCommand('doctrine:fixtures:load -n --purge-with-truncate');
    // self::runCommand('doctrine:database:create');
    // self::runCommand('doctrine:schema:update --force');
    // self::runCommand('doctrine:fixtures:load -n --purge-with-truncate');
  }

  protected static function runCommand($command)
  {
    $command = sprintf('%s --quiet', $command);

    return self::getApplication()->run(new StringInput($command));
  }

  protected static function getApplication()
  {
    if (null === self::$application) {
      $client = static::createClient();
      self::bootKernel();

      self::$application = new Application($client->getKernel());
      self::$application->setAutoExit(false);
    }

    return self::$application;
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

  public function testSuccessRegistration(): void
  {
    $client = static::createClient();

    $data = [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'email' => 'test-success-reg@example.com',
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
    $this->assertEquals('{}', $response);
  }

  public function testRegistrationWithExistingEmail(): void
  {
    $client = static::createClient();

    // create a new client
    $data = [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'email' => 'test-already-exist@example.com',
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

    // check success created
    $this->assertResponseStatusCodeSame(201);

    // try again
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
    $this->assertEquals('{"error":"userAlreadyExist"}', $response);
  }

}
