<?php

namespace App\Tests\Functional;

use App\Entity\Testimonials;
use App\Entity\User;
use App\Repository\TestimonialsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestimonialsControllerTest extends WebTestCase
{
    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;
    
    /** @var UserRepository $userRepository  */
    private UserRepository $userRepository;
    
    /** @var TestimonialsRepository $testimonialsRepository  */
    private TestimonialsRepository $testimonialsRepository;
    
    /**
     * @var User
     */
    private User $user;
    /**
     * @var User
     */
    private User $user_to;
    
    protected function setUp()
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->testimonialsRepository = $this->entityManager->getRepository(Testimonials::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->user = $this->getUser();
        $this->user_to =$this->getUser(true);
        self::ensureKernelShutdown();
    }
    
    protected function tearDown(): void
    {
        $this->removeTestimonial();
    }
    
    public function testAddReturnSuccessResponse()
    {
        $client = static::createClient();
    
        $client->request('POST', "/testimonials/{$this->user_to->getId()}",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'text'      => 'Testimonial from phpunit',
                'rating'    => 3
            ])
        );
        
        $response = $client->getResponse();
        
        $this->assertJson($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertContains( json_encode(['status' => 'success']), $response->getContent());
    }
    
    public function testAddReturnErrorDuplicate()
    {
        $originClient = static::createClient();
        self::ensureKernelShutdown();
        $duplicateClient = static::createClient();
        
        $originClient->request('POST', "/testimonials/{$this->user_to->getId()}",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'text'      => 'Testimonial from phpunit',
                'rating'    => 3
            ])
        );
        
        $duplicateClient->request('POST', "/testimonials/{$this->user_to->getId()}",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'text'      => 'Testimonial from phpunit',
                'rating'    => 3
            ])
        );
        
        $response = $duplicateClient->getResponse();
        
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertContains('User already left the review for this mentor', $response->getContent());
    }
    
    public function testAddReturnErrorNonMentor()
    {
        $client = static::createClient();
        
        $user_to = $this->userRepository->findOneBy([
            'active' => true,
        ], [
            'id' => 'desc'
        ]);
    
        $client->request('POST', "/testimonials/{$user_to->getId()}",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'text'      => 'Testimonial from phpunit',
                'rating'    => 3
            ])
        );
    
        $response = $client->getResponse();
    
        $this->assertJson($response->getContent());
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertContains( 'Only mentor can have a review', $response->getContent());
    }
    
    public function testAddReturnErrorYourself()
    {
        $client = static::createClient();
    
        $client->request('POST', "/testimonials/{$this->user->getId()}",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'text'      => 'Testimonial from phpunit',
                'rating'    => 3
            ])
        );
    
        $response = $client->getResponse();
    
        $this->assertJson($response->getContent());
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertContains( 'Forbidden to write testimonials for yourself', $response->getContent());
    }
    
    private function removeTestimonial()
    {
        $testimonial = $this->testimonialsRepository->findOneBy([
            'text'      => 'Testimonial from phpunit',
            'user_from' => $this->user,
            'user_to'   => $this->user_to
        ]);
        if($testimonial) {
            $this->entityManager->remove($testimonial);
            $this->entityManager->flush();
        }
    }
    
    private function getUser(bool $to = false) : User
    {
        if($to) {
            $users = $this->userRepository->getMentors(1);
            return $users[0];
        }
        return $this->userRepository->findOneBy([
            'active' => true,
        ]);
    }
}