<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @var \App\Repository\UserRepository|\Doctrine\Persistence\ObjectRepository
     */
    private UserRepository $userRepository;

    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;

    protected function setUp()
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
        self::ensureKernelShutdown();
    }

    private function getUser(bool $active = true) : User
    {
        return $this->userRepository->findOneBy([
            'active' => $active
        ]);
    }

    /**
     * Login Section
     */

    public function testLoginReturnSuccessResponse()
    {
        $client = static::createClient();
        $user = $this->getUser();

        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'email'     => $user->getEmail(),
            'password'  => '111111'
        ]));
        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals(200, $status, "Expected status 200, got {$status}");
    }

    public function testLoginSuccessReturnToken()
    {
        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'email'     => $this->getUser()->getEmail(),
            'password'  => '111111'
        ]));

        $this->assertContains('token', $client->getResponse()->getContent(),'Response doesn\'t have token');
    }

    public function testLoginReturnNotFound()
    {
        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'email'     => 'nonexistint@mail.not',
            'password'  => '123123'
        ]));

        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals(404, $status, "Expected status 404, got {$status}");
    }

    public function testLoginReturnBadRequest()
    {
        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'email'     => $this->getUser()->getEmail(),
            'password'  => '123123'
        ]));

        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals(400, $status, "Expected status 400, got {$status}");
    }

    /**
     * Registration section
     */

    public function testRegisterReturnSuccessStatus()
    {
        $request = [
            'email'       => 'phpunit@mail.test',
            'password'    => '111111111',
            'name'        => 'PhpUnit',
            'username'    => 'UserNameForPhpUnit'
        ];

        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], [], json_encode($request));

        $user = $this->userRepository->findOneBy([
            'email' => 'phpunit@mail.test'
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function testRegisterCreateUser()
    {
        $request = [
            'email'       => 'phpunit@mail.test',
            'password'    => '111111111',
            'name'        => 'PhpUnit',
            'username'    => 'UserNameForPhpUnit'
        ];

        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], [], json_encode($request));

        $user = $this->userRepository->findOneBy([
            'email' => 'phpunit@mail.test'
        ]);

        $this->assertEquals('phpunit@mail.test', $user->getEmail());
        $this->assertEquals('UserNameForPhpUnit', $user->getUsername());
        $this->assertEquals('PhpUnit', $user->getProfile()->getName());

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function testRegisterValidationWorks()
    {
        $request = [
            'email'       => 'phpunitmail.test',
            'password'    => '1',
            'name'        => 'PhpUnit',
            'username'    => 'UserNameForPhpUnit'
        ];

        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], [], json_encode($request));

        $this->assertEquals(400,$client->getResponse()->getStatusCode(), 'Expected 400 response status code');
    }

    public function testRegisterValidateDuplicateData()
    {
        $user = $this->getUser();
        $request = [
            'email'       => $user->getEmail(),
            'password'    => '111111111',
            'name'        => 'PhpUnit',
            'username'    => $user->getUsername()
        ];

        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], [], json_encode($request));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Expected 400 response status code');
    }

    /**
     * Activation section
     */

    public function testActivateReturnSuccessStatus()
    {
        $user = $this->getUser(false);

        $client = static::createClient();
        $client->request('GET', "/auth/activate/{$user->getActivationToken()}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Expected 200 status code');

    }

    public function testActivateReturnSuccessJson()
    {
        $user = $this->getUser(false);

        $client = static::createClient();
        $client->request('GET', "/auth/activate/{$user->getActivationToken()}");

        $this->assertContains(json_encode(['result' => 'success']),$client->getResponse()->getContent());

    }

    public function testActivateReturnNotFound()
    {
        $client = static::createClient();
        $client->request('GET', "/auth/activate/nonExistingToken");

        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }
}