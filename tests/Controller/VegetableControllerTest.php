<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixtureTestCase\FixtureAwareTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\RouterInterface;

class VegetableControllerTest extends FixtureAwareTestCase
{
    protected KernelBrowser $client;
    protected RouterInterface $router;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = $this->getContainer()->get('router');

        parent::setUp();
    }

    public function testPostVegetable(): void
    {
        $url = $this->router->generate('vegetable_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "Pepper","gram": 150000}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response[0];

        $this->assertSame(200, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('dateTimeAdded', $fruit);
        $this->assertSame(201, $response[1]);
    }

    public function testGetVegetable(): void
    {
        $url = $this->router->generate('vegetable_list');
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response[0]->vegetables[0];

        $this->assertSame(200, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('dateTimeAdded', $fruit);
        $this->assertSame(200, $response[1]);
    }

    public function testDeleteVegetable(): void
    {
        $url = $this->router->generate('vegetable_delete', ['id' => 1]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertSame(200, $statusCode);
        $this->assertNull($response[0]);
        $this->assertSame(204, $response[1]);
    }
}
