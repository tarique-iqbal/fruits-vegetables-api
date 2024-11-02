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
        $this->assertObjectHasProperty('createdAt', $vegetable);
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
        $this->assertObjectHasProperty('createdAt', $vegetable);
    }

    public static function pageNotFoundProvider(): array
    {
        return [
            [
                99, Response::HTTP_NOT_FOUND
            ],
            [
                '99', Response::HTTP_NOT_FOUND
            ],
        ];
    }

    /**
     * @dataProvider pageNotFoundProvider
     */
    public function testGetVegetablesPageNotFound(int|string $page, int $expectedCode): void
    {
        $url = $this->router->generate('vegetable_list', ['page' => $page]);
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($expectedCode, $statusCode);
        $this->assertStringContainsString((string) $page, $response->error);
    }

    public static function invalidParameterProvider(): array
    {
        return [
            [
                '03', 'gram2', Response::HTTP_BAD_REQUEST
            ],
            [
                'str3', 'kilogram2', Response::HTTP_BAD_REQUEST
            ],
            [
                '@#^3$%', 'random', Response::HTTP_BAD_REQUEST
            ],
        ];
    }

    /**
     * @dataProvider invalidParameterProvider
     */
    public function testGetVegetablesInvalidParameter(string $page, string $unit, int $expectedCode): void
    {
        $url = $this->router->generate('vegetable_list', ['page' => $page, 'unit' => $unit]);
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($expectedCode, $statusCode);
        $this->assertCount(2, $errors);
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
