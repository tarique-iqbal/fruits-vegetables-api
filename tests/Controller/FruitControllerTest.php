<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixtureTestCase\FixtureAwareTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\RouterInterface;

class FruitControllerTest extends FixtureAwareTestCase
{
    protected KernelBrowser $client;
    protected RouterInterface $router;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = $this->getContainer()->get('router');

        parent::setUp();
    }

    public function testPostFruit(): void
    {
        $url = $this->router->generate('fruit_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "Kiwi","gram": 10000}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response[0];

        $this->assertEquals(200, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('dateTimeAdded', $fruit);
        $this->assertSame(201, $response[1]);
    }

    public function testGetFruits(): void
    {
        $url = $this->router->generate('fruit_list');
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response[0]->fruits[0];

        $this->assertEquals(200, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('dateTimeAdded', $fruit);
        $this->assertSame(200, $response[1]);
    }

    public function testDeleteFruit(): void
    {
        $url = $this->router->generate('fruit_delete', ['id' => 1]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(200, $statusCode);
        $this->assertNull($response[0]);
        $this->assertSame(204, $response[1]);
    }

    public function testDeleteFruitNotFound(): void
    {
        $url = $this->router->generate('fruit_delete', ['id' => 0]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();

        $this->assertEquals(404, $statusCode);
    }
}
