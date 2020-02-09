<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Offers;
use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompanyFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $ca = new Company;
        $ca->setCompanyName('Web Dev & Co.');
        $ca->setFirstname('Stephane');
        $ca->setLastname('Kergoat');
        $ca->setAddress('1 rue du chemin vert');
        $ca->setZipCode('75000');
        $ca->setCity('Paris');
        $ca->setTelNumber('06.85.83.93.34');
        $ca->setSiret('1234567890');

        $user = new User; 
        $user->setEmail('company@gmail.com');
        $user->setRoles(['ROLE_COMPANY']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'company'
        ));

            $offer1 = new Offers;
            $offer1->setTitle('developpeur front-end'); 
            $offer1->setDomain('informatique');
            $offer1->setLocation('Paris'); 
            $offer1->setDateStart(new \DateTime('now')); 
            $offer1->setDateEnd(new \DateTime('now'));
            $offer1->setDescription('Un super bon job !'); 

            $offer2 = new Offers;
            $offer2->setTitle('developpeur front-end'); 
            $offer2->setDomain('informatique');
            $offer2->setLocation('Paris'); 
            $offer2->setDateStart(new \DateTime('now')); 
            $offer2->setDateEnd(new \DateTime('now'));
            $offer2->setDescription('Un super bon job !'); 

            $offer1->setCompany($ca);
            $offer2->setCompany($ca);

            $manager->persist($offer1);
            $manager->persist($offer2);

        $ca->setUser($user);

        $manager->persist($ca);
        $manager->persist($user);

        $manager->flush();
    }
}
