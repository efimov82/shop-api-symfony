<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private string $apiHost = '';

    protected function setUp(): void
    {
        // $this->apiHost = $_SERVER['REST_API_HOST'];
    }

    public function testGetHomeWithoutToken(): void
    {
        $client = static::createClient();

        // $request = $client->post('/api/programmers', null, json_encode($data));
        // $request = $client->request('GET', 'http://localhost:3000/api/v1/products/1'); //, json_encode($data)
        // $response = $request->send();

        // $this->assertEquals(201, $response->getStatusCode());
        // // $this->assertTrue($response->hasHeader('Location'));
        // $data = json_decode($response->getBody(true), true);
        // $this->assertArrayHasKey('nickname', $data);

        // $crawler = $client->request('GET', 'http://localhost:3000/api/v1/home');
        $client->request('GET', "/api/v1/user/home");
        $response = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json', 'not correct json response');
        $this->assertEquals('{"message":"Token not provided"}', $response);
    }
}
