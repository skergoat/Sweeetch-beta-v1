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
        for($i = 1 ; $i < 5 ; $i++) {
            $ca = new Company;
            $ca->setCompanyName('Company'.$i);
            $ca->setFirstname('Stephane');
            $ca->setLastname('Kergoat');
            $ca->setAddress('1 rue du chemin vert');
            $ca->setZipCode('75000');
            $ca->setCity('Paris');
            $ca->setTelNumber('06.85.83.93.34');
            $ca->setSiret('49248704350061');

            $user = new User; 
            $user->setEmail('company' . $i . '@gmail.com');
            $user->setRoles(['ROLE_COMPANY']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'test'
            ));

                $offer1 = new Offers;
                $offer1->setTitle('Développeur front-end-' . $i); 
                $offer1->setDomain('informatique');
                $offer1->setLocation('Paris'); 
                $offer1->setDateStart(new \DateTime('now')); 
                $offer1->setDateEnd(new \DateTime('now'));
                $offer1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tincidunt finibus ligula. Proin nec mi nec massa posuere commodo et sed mi. Aliquam varius, quam ut consequat hendrerit, velit dolor ornare tellus, non pharetra lectus turpis et mi. Fusce at lectus non velit accumsan dictum nec vitae ante. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi nec dictum nisl. Nunc mollis commodo nulla, vel cursus mauris pretium eget. Vestibulum non turpis venenatis, varius massa at, mattis nisi. Aliquam turpis elit, vehicula et consectetur ut, dapibus a dui. Vestibulum sit amet justo bibendum, lacinia augue a, consequat erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec luctus tincidunt scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'); 
                $offer1->setState(false);

                $offer2 = new Offers;
                $offer2->setTitle('Développeur front-end-' . $i); 
                $offer2->setDomain('informatique');
                $offer2->setLocation('Paris'); 
                $offer2->setDateStart(new \DateTime('now')); 
                $offer2->setDateEnd(new \DateTime('now'));
                $offer2->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tincidunt finibus ligula. Proin nec mi nec massa posuere commodo et sed mi. Aliquam varius, quam ut consequat hendrerit, velit dolor ornare tellus, non pharetra lectus turpis et mi. Fusce at lectus non velit accumsan dictum nec vitae ante. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi nec dictum nisl. Nunc mollis commodo nulla, vel cursus mauris pretium eget. Vestibulum non turpis venenatis, varius massa at, mattis nisi. Aliquam turpis elit, vehicula et consectetur ut, dapibus a dui. Vestibulum sit amet justo bibendum, lacinia augue a, consequat erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec luctus tincidunt scelerisque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'); 
                $offer2->setState(false);

                $offer1->setCompany($ca);
                $offer2->setCompany($ca);

                $manager->persist($offer1);
                $manager->persist($offer2);

            $ca->setUser($user);

            $manager->persist($ca);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
