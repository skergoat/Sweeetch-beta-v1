<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // for($i = 1 ; $i < 5 ; $i++) {

        //     $user = new User; 
        //     $user->setEmail('admin' . $i . '@gmail.com');
        //     $user->setRoles(['ROLE_ADMIN']);
        //     $user->setConfirmed(true);
        //     $user->setPassword($this->passwordEncoder->encodePassword(
        //         $user,
        //         'admin'
        //     ));

        //     $manager->persist($user);
        // }

        $superAdmin = new User; 
        $superAdmin->setEmail('kergoane@gmail.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setConfirmed(true);
        $superAdmin->setPassword($this->passwordEncoder->encodePassword(
            $superAdmin,
            '9195285B65x9@'
        ));

        $manager->persist($superAdmin);

        $manager->flush();
    }
}
