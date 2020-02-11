<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\Profile;
use App\Entity\Student;
use App\Entity\Language;
use App\Entity\Education;
use App\Entity\StudentCard;
use App\Entity\ProofHabitation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StudentFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for($i = 1 ; $i < 3 ; $i++) {

            $student = new Student;
            $student->setName('student' . $i);
            $student->setLastname('lastname' . $i);
            $student->setAdress($i . 'rue du chemin vert');
            $student->setZipCode('7' . $i . '500');
            $student->setTelNumber('0' . $i . '.89.78.67.43');
            $student->setCity('Paris');

            $user = new User; 
            $user->setEmail('student' . $i . '@gmail.com');
            $user->setRoles(['ROLE_STUDENT', 'ROLE_NEW']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'student'
            ));

            $profile = new Profile;
            $profile->setDomain('development');
            $profile->setArea('Paris');
            
                $language1 = new Language;
                $language1->setLanguageName('anglais'); 
                $language1->setLevel('bon'); 

                $language2 = new Language;
                $language2->setLanguageName('viet'); 
                $language2->setLevel('bon'); 

                $date = new \Datetime('2019-03-06');
                $date2 = new \Datetime('2020-04-06');

                $education1 = new Education;
                $education1->setTitle('Baccalaureat general - S'); 
                $education1->setSchool('Lycee du Bon Pasteur');
                $education1->setDateStart($date);
                $education1->setDateEnd($date2); 
                $education1->setCurrent(false); 

                $education2 = new Education;
                $education2->setTitle('L1 developement web'); 
                $education2->setSchool('Universite de Paris'); 
                $education2->setDateStart($date2); 
                $education2->setCurrent(true);

                $language1->setProfile($profile);
                $language2->setProfile($profile);

                $education1->setProfile($profile);
                $education2->setProfile($profile);

                $manager->persist($language1);
                $manager->persist($language2);

                $manager->persist($education1);
                $manager->persist($education2);
            
            $resume = new Resume;
            $resume->setFileName('https://picsum.photos/536/354');
            $resume->setOriginalFilename('https://picsum.photos/536/354');
            $resume->setMimeType('pdf');

            $IdCard = new IdCard;
            $IdCard->setFileName('https://picsum.photos/536/354');
            $IdCard->setOriginalFilename('https://picsum.photos/536/354');
            $IdCard->setMimeType('pdf');

            $studentCard = new StudentCard;
            $studentCard->setFileName('https://picsum.photos/536/354');
            $studentCard->setOriginalFilename('https://picsum.photos/536/354');
            $studentCard->setMimeType('pdf');

            $proofHabitation = new ProofHabitation;
            $proofHabitation->setFileName('https://picsum.photos/536/354');
            $proofHabitation->setOriginalFilename('https://picsum.photos/536/354');
            $proofHabitation->setMimeType('pdf');

            $student->setUser($user);
            $student->setProfile($profile);
            $student->setResume($resume);
            $student->setIdCard($IdCard);
            $student->setStudentCard($studentCard);
            $student->setProofHabitation($proofHabitation);

            $manager->persist($user);
            $manager->persist($student);
        }

        $manager->flush();
    }
}
