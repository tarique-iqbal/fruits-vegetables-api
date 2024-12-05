<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\FruitFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FruitListControllerTest extends WebTestCase
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

    public function testGetFruits(): void
    {
        $url = $this->router->generate('fruit_list');
        $this->client->request('GET', $url);

        $response = json_decode($this->client->getResponse()->getContent());
        $fruit = $response->fruits[0];

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
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

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame($expectedCode);
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

        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertCount(2, $errors);
    }
}
