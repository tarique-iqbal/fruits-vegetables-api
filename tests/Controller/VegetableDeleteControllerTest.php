<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class VegetableDeleteControllerTest extends WebTestCase
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

    public function testDeleteVegetable(): void
    {
        $url = $this->router->generate('vegetable_delete', ['id' => 1]);
        $this->client->request('DELETE', $url);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertNull($response);
    }

    public function testDeleteVegetableNotFound(): void
    {
        $url = $this->router->generate('vegetable_delete', ['id' => 0]);
        $this->client->request('DELETE', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
