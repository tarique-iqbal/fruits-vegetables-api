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
            content: '{"name": "Pepper","type": "vegetable","quantity": 150,"unit": "kg"}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $vegetable = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_CREATED, $statusCode);
        $this->assertObjectHasProperty('name', $vegetable);
        $this->assertObjectHasProperty('alias', $vegetable);
        $this->assertObjectHasProperty('gram', $vegetable);
        $this->assertObjectHasProperty('dateTimeAdded', $vegetable);
    }

    public function testPostVegetableInvalidInput(): void
    {
        $url = $this->router->generate('vegetable_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "","type": "vegetable","quantity": 0,"unit": "lb"}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $statusCode);
        $this->assertCount(5, $errors);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $errors);
    }

    public function testGetVegetables(): void
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

    public function testGetVegetablesInvalidPage(): void
    {
        $url = $this->router->generate('vegetable_list', ['page' => 99]);
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $statusCode);
        $this->assertStringContainsString('99', $response->error);
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
