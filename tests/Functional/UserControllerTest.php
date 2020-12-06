<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \App\Repository\UserRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $userRepository;

    protected function setUp()
    {
        self::bootKernel();
        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $entityManager->getRepository(User::class);
        self::ensureKernelShutdown();
    }

    public function testGetUserPublicInfoStatus()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Test doesn\'t get 200 status');
    }

    public function testGetUserPublicInfoIsJson()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent(), 'Response are not JSON');
    }

    public function testGetUserPublicInfoAuth()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "bad token"
        ]);

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Test doesn\'t get 401 status');
    }

    public function testGetUserPublicInfoJsonContains()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $response = $client->getResponse()->getContent();

        $fields = [
            'email', 'username', 'created_at',
            'name', 'surname', 'phone', 'mentor',
            'skills'
        ];

        foreach ($fields as $field) {
            $this->assertContains($field, $response, "Response doesn't contains {$field} field");
        }

    }

    public function testGetUserPublicInfoReturnNotFound()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/01010101",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetTestimonialsStatus()
    {
        $user = $this->getUser(true);
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetTestimonialsReturnNotFound()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('GET', "/user/01010101/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetTestimonialsIsJson()
    {
        $user = $this->getUser(true);
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent(), 'Response are not JSON');
    }

    public function testGetTestimonialsJsonContains()
    {
        $user = $this->getUser(true);
        $client = static::createClient();

        $client->request('GET', "/user/{$user->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $response = $client->getResponse()->getContent();
        $fields = ['result', 'user_from', 'from_name', 'text', 'rating'];

        foreach ($fields as $field) {
            $this->assertContains($field, $response, "Response doesn't contains {$field} field");
        }
    }

    private function getUser(bool $with_testimonials = false) : User
    {
        if($with_testimonials) {
            $users = $this->userRepository->getUsersWithTestimonials(1,1,0);
        }else{
            $users = $this->userRepository->findBy([
                'active' => true,
            ], null, 1);
        }

        return $users[0];
    }
}
