<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmployeeFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $employe = new Employee();
        $employe->setRoles(["ROLE_DIRECTION"]);
        $employe->setPassword($this->hasher->hashPassword($employe, "admin123@"));
        $employe->setFirstname('admin');
        $employe->setLastname('admin');
        $employe->setEmail("admin@deloitte.com");
        $employe->setSector("Direction");
        $employe->setPhoto("admin.png");

        $manager->persist($employe);

        $manager->flush();
    }
}
