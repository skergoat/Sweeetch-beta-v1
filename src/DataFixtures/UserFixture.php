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
        $superAdmin = new User; 
        $superAdmin->setEmail('super_admin@gmail.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setConfirmed(true);
        $superAdmin->setPassword($this->passwordEncoder->encodePassword(
            $superAdmin,
            'super_admin'
        ));

        $manager->persist($superAdmin);

        $manager->flush();
    }
}
