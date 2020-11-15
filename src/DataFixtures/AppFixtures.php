<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use App\Entity\Skill;
use App\Entity\User;
use App\Services\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Provider\Internet;
use Faker\Provider\Person;
use Faker\Provider\PhoneNumber;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var array
     */
    private array $users;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadSkills($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        $faker = new Generator();
        $faker->addProvider(new Internet($faker));
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new PhoneNumber($faker));
        $tokenGenerator = new TokenGenerator();

        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $profile = new Profile();
            $active = rand(0,1);

            $user->setEmail($faker->freeEmail());
            $user->setPassword($this->encoder->encodePassword($user,'k.,f1321'));
            $user->setUsername($faker->userName());
            if($active) {
                $user->setActive(true);
                $user->setSessionToken( $tokenGenerator->generateToken(50) );
                $profile->setSurname($faker->lastName());
                $profile->setPhone($faker->phoneNumber());
            }else{
                $user->setActivationToken($tokenGenerator->generateToken(50));
            }

            $profile->setName($faker->firstName());
            $profile->setUser($user);

            $manager->persist($user);
            $manager->persist($profile);
            $manager->flush();

            $this->users[] = $user;
        }
    }

    public function loadSkills(ObjectManager $manager)
    {
        $skills = ['js', 'php', 'c#', 'c', 'c++', 'abode photoshop', 'algorithms', 'data scents'];

        foreach ($skills as $item) {
            $skill = new Skill();
            $skill->setName($item);
            $skill->setValid(true);

            $manager->persist($skill);
            foreach ($this->users as /** @var User $user */ $user) {
                if(rand(0,1)) {
                    $user->getSkills()->add($skill);
                }
            }
            $manager->flush();
        }
    }
}
