<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    public function getLoginCheckJWT(): string
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/login_check',[
            'headers' => [
            'Content-Type' => 'application/ld+json',
            'Accept' => 'application/ld+json'
        ],
        'json' => [
            'email' => 'admin@mail.com',
            'password' => 'admin123',
        ]
        ]);

        $token = $response->toArray()['token'];

        return $token;

    }

    public function testCreateUserWithAuth(): void
    {
        $client = static::createClient();

        $token = $this->getLoginCheckJWT();

        $email = 'email@test.com';

        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'accept' => 'application/ld+json',
                'Authorization' => 'Bearer '.$token
            ],
            'json' => [
                'email' => $email,
                'password' => '123456'
            ]
        ]);

        $this->assertSame($email, $response->toArray()['email']);
    }

}
