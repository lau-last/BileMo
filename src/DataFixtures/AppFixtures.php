<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Device;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Device($faker));

        $user = new User();
        $user
            ->setEmail('user@bilemo.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $admin = new User();
        $admin
            ->setEmail('admin@bilemo.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        for ($i = 0; $i < 50; $i++) {
            $device = new Device();
            $device
                ->setBuildNumber($faker->deviceBuildNumber())
                ->setManufacturer($faker->deviceManufacturer())
                ->setModelName($faker->deviceModelName())
                ->setPlatform($faker->devicePlatform())
                ->setSerialNumber($faker->deviceSerialNumber())
                ->setVersion($faker->deviceVersion());
            $manager->persist($device);
        }

        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $customer = new Customer();
            $customer
                ->setEmail($faker->email())
                ->setName($faker->name($gender))
                ->setAddress($faker->address())
                ->setGender($gender)
                ->setFirstname($faker->firstName($gender))
                ->setPhoneNumber($faker->phoneNumber())
                ->setCreatedAt($faker->dateTime())
                ->setDateOfBirth($faker->dateTime())
                ->setUser($faker->randomElement([$admin, $user]));
            $manager->persist($customer);
        }

        $manager->flush();
    }


}
