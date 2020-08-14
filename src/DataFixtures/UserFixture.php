<?php
namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

// This Fixtures page is used to load users to database

class UserFixture extends Fixture {	
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
	
    //	This function is used to build the user object used and insert users in to database.
	/* Role '1' is for Admin and Role '2' is for frontend user */
    public function load(ObjectManager $manager) {
        $user = new user();
        $user->setUserName('admin@gmail.com');
        $user->setEmail('admin@gmail.com');
        $user->setLastName('admin');
        $user->setFirstName('admin');
        $user->setRoles([1]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin@123$'));
        $user->setDateAdded(new \DateTime());
        $manager->persist($user);
        $manager->flush();
        $userNew = new user();
        $userNew->setUserName('user1@gmail.com');
        $userNew->setEmail('user1@gmail.com');
        $userNew->setLastName('User');
        $userNew->setFirstName('Test');
        $userNew->setRoles([2]);
        $userNew->setPassword($this->passwordEncoder->encodePassword($userNew, 'user1@123$'));
        $userNew->setDateAdded(new \DateTime());
        $manager->persist($userNew);
        $manager->flush();
    }
}
