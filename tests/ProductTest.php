<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
