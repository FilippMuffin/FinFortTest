<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HttpCheckController extends WebTestCase
{
    public function testCheckRequest()
    {
        $client = static::createClient([], [
            'HTTP_HOST'       => 'localhost:18901',
        ]);
        $crawler = $client->request('POST', '/addresses/check', content:
            json_encode([
                'https://google.com'
            ])
        );
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
