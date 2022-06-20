<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;

class UserResourceTest extends CustomApiTestCase
{
    public function testCreateUser()
    {
        dump('test 0');
        $client = self::createClient();
        dump('test 1 ');
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'cheeseplease@example.com',
                'username' => 'cheeseplease',
                'password' => 'brie'
            ]
        ]);
        dump('test 2 ');
        $this->assertResponseStatusCodeSame(201);
        dump('test 3 ');
        $this->logIn($client, 'cheeseplease@example.com', 'brie');
        dump('test 4 ');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logIn($client, $user);

        $client->request('PUT', '/api/users/' . $user->getId(), [
            'json' => [
                'username' => 'newusername',
                'roles' => ['ROLE_ADMIN'] // will be ignored
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'newusername'
        ]);

        $user->refresh();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create(['phoneNumber' => '555.123.4567']);
        $authenticatedUser = UserFactory::new()->create();
        $this->logIn($client, $authenticatedUser);

        $client->request('GET', '/api/users/' . $user->getId());
        $this->assertJsonContains([
            'username' => $user->getUsername(),
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);
        $this->assertJsonContains([
            'isMe' => false,
        ]);

        // refresh the user & elevate
        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();
        $this->logIn($client, $user);

        $client->request('GET', '/api/users/' . $user->getId());
        $this->assertJsonContains([
            'phoneNumber' => '555.123.4567',
            'isMe' => true,
        ]);
    }
}
