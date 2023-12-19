<?php

namespace App\DataFixtures;

use App\Entity\JobType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
;

class JobTypes extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jobTypesData = ['Full Time', 'Part Time', 'Freelancer'];

        foreach ($jobTypesData as $jobTypeName) {
            $jobType = new JobType();
            $jobType->setName($jobTypeName);
            $manager->persist($jobType);
        }

        $manager->flush();
    }
}
