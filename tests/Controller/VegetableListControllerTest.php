<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class VegetableListControllerTest extends WebTestCase
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
            ->loadFixtures([VegetableFixtures::class]);
    }

    public function testGetVegetables(): void
    {
        $url = $this->router->generate('vegetable_list');
        $this->client->request('GET', $url);

        $response = json_decode($this->client->getResponse()->getContent());
        $vegetable = $response->vegetables[0];

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
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
    public function testGetVegetablesInvalidParameter(string $page, string $unit, int $expectedCode): void
    {
        $url = $this->router->generate('vegetable_list', ['page' => $page, 'unit' => $unit]);
        $this->client->request('GET', $url);

        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertCount(2, $errors);
    }
}
