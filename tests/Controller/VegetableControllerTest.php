<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixtureTest\FixtureAwareTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
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
        $vegetable = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_CREATED, $statusCode);
        $this->assertObjectHasProperty('name', $vegetable);
        $this->assertObjectHasProperty('alias', $vegetable);
        $this->assertObjectHasProperty('gram', $vegetable);
        $this->assertObjectHasProperty('dateTimeAdded', $vegetable);
    }

    public function testGetVegetable(): void
    {
        $url = $this->router->generate('vegetable_list');
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $vegetable = $response->vegetables[0];

        $this->assertEquals(Response::HTTP_OK, $statusCode);
        $this->assertObjectHasProperty('name', $vegetable);
        $this->assertObjectHasProperty('alias', $vegetable);
        $this->assertObjectHasProperty('gram', $vegetable);
        $this->assertObjectHasProperty('dateTimeAdded', $vegetable);
    }

    public function testDeleteVegetable(): void
    {
        $url = $this->router->generate('vegetable_delete', ['id' => 1]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($response);
    }

    public function testDeleteVegetableNotFound(): void
    {
        $url = $this->router->generate('vegetable_delete', ['id' => 0]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $statusCode);
    }
}
