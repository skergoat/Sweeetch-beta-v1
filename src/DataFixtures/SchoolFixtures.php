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

        for($i = 1 ; $i < 5 ; $i++) {

            $ca = new School;
            $ca->setCompanyName('School'.$i);
            $ca->setFirstname('Stephane');
            $ca->setLastname('Kergoat');
            $ca->setAddress('1 rue du chemin vert');
            $ca->setZipCode('75000');
            $ca->setCity('Paris');
            $ca->setTelNumber('06.85.83.93.34');
            $ca->setSiret('49248704350061');

            $user = new User; 
            $user->setEmail('school' . $i . '@gmail.com');
            $user->setRoles(['ROLE_SCHOOL']);
            $user->setConfirmed(false);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'ste'
            ));

                $offer1 = new Studies;
                $offer1->setTitle('study'.$i); 
                $offer1->setDomain('domaine-'.$i.'-1'); 
                $offer1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tincidunt finibus ligula. Proin nec mi nec massa posuere commodo et sed mi. Aliquam varius, quam ut consequat hendrerit, velit dolor ornare tellus, non pharetra lectus turpis et mi. Fusce at lectus non velit accumsan dictum nec vitae ante. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi nec dictum nisl. Nunc mollis commodo nulla, vel cursus mauris pretium eget. Vestibulum non turpis venenatis, varius massa at, mattis nisi. Aliquam turpis elit, vehicula et consectetur ut, dapibus a dui. Vestibulum sit amet justo bibendum, lacinia augue a, consequat erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec luctus tincidunt scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'); 

                $offer2 = new Studies;
                $offer2->setTitle('study'.$i);
                $offer2->setDomain('domaine-'.$i.'-2');  
                $offer2->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tincidunt finibus ligula. Proin nec mi nec massa posuere commodo et sed mi. Aliquam varius, quam ut consequat hendrerit, velit dolor ornare tellus, non pharetra lectus turpis et mi. Fusce at lectus non velit accumsan dictum nec vitae ante. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi nec dictum nisl. Nunc mollis commodo nulla, vel cursus mauris pretium eget. Vestibulum non turpis venenatis, varius massa at, mattis nisi. Aliquam turpis elit, vehicula et consectetur ut, dapibus a dui. Vestibulum sit amet justo bibendum, lacinia augue a, consequat erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec luctus tincidunt scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'); 

                $offer1->setSchool($ca);
                $offer2->setSchool($ca);

                $manager->persist($offer1);
                $manager->persist($offer2);

            $ca->setUser($user);

            $manager->persist($ca);
            $manager->persist($user);

        }

        $manager->flush();
    }
}
