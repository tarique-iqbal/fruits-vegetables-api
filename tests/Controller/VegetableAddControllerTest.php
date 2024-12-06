<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class VegetableAddControllerTest extends WebTestCase
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

    public function testPostVegetable(): void
    {
        $url = $this->router->generate('vegetable_add');
        $this->client->request(
            'POST',
            uri: $url,
            content: '{"name": "Pepper","type": "vegetable","quantity": 150,"unit": "kg"}'
        );

        $vegetable = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
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

        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertCount(5, $errors);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $errors);
    }
}
