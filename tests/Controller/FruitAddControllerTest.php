<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\FruitFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FruitAddControllerTest extends WebTestCase
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

        $fruit = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
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

        $errors = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertCount(5, $errors);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $errors);
    }
}
