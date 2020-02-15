<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Offers;
use App\Entity\School;
use App\Entity\Company;
use App\Entity\Studies;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SchoolFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $ca = new School;
        $ca->setCompanyName('Web Dev & Co.');
        $ca->setFirstname('Stephane');
        $ca->setLastname('Kergoat');
        $ca->setAddress('1 rue du chemin vert');
        $ca->setZipCode('75000');
        $ca->setCity('Paris');
        $ca->setTelNumber('06.85.83.93.34');
        $ca->setSiret('1234567890');

        $user = new User; 
        $user->setEmail('school@gmail.com');
        $user->setRoles(['ROLE_SCHOOL']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'school'
        ));

            // $offer1 = new Studies;
            // $offer1->setTitle('developpeur web'); 
            // $offer1->setDescription('developpeur web'); 
           
            // $offer2 = new Studies;
            // $offer2->setTitle('developpeur php'); 
            // $offer2->setDescription('developpeur web'); 

            // $offer1->setSchool($ca);
            // $offer2->setSchool($ca);

            // $manager->persist($offer1);
            // $manager->persist($offer2);

        $ca->setUser($user);

        $manager->persist($ca);
        $manager->persist($user);

        $manager->flush();
    }
}
