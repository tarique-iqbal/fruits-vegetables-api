<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\FruitFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FruitControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected RouterInterface $router;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->router = $container->get('router');

        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([FruitFixtures::class]);
    }

    public function testPostFruit(): void
    {
        $url = $this->router->generate('fruit_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "Kiwi","type": "fruit","quantity": 10,"unit": "kg"}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $fruit = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_CREATED, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('createdAt', $fruit);
    }

    public function testPostFruitInvalidInput(): void
    {
        $url = $this->router->generate('fruit_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "","type": "fruit","quantity": 0,"unit": "lb"}'
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $statusCode);
        $this->assertCount(5, $errors);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $errors);
    }

    public function testGetFruits(): void
    {
        $url = $this->router->generate('fruit_list');
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response->fruits[0];

        $this->assertEquals(Response::HTTP_OK, $statusCode);
        $this->assertObjectHasProperty('name', $fruit);
        $this->assertObjectHasProperty('alias', $fruit);
        $this->assertObjectHasProperty('gram', $fruit);
        $this->assertObjectHasProperty('createdAt', $fruit);
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
    public function testGetFruitsPageNotFound(int|string $page, int $expectedCode): void
    {
        $url = $this->router->generate('fruit_list', ['page' => $page]);
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
    public function testGetFruitsInvalidParameter(string $page, string $unit, int $expectedCode): void
    {
        $url = $this->router->generate('fruit_list', ['page' => $page, 'unit' => $unit]);
        $this->client->request('GET', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($expectedCode, $statusCode);
        $this->assertCount(2, $errors);
    }

    public function testDeleteFruit(): void
    {
        $url = $this->router->generate('fruit_delete', ['id' => 1]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($response);
    }

    public function testDeleteFruitNotFound(): void
    {
        $url = $this->router->generate('fruit_delete', ['id' => 0]);
        $this->client->request('DELETE', $url);

        $statusCode = $this->client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $statusCode);
    }
}
