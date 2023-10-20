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

        $admin = new User();
        $admin
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail('admin@bilemo.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $user = new User();
        $user
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail('user@bilemo.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

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
            $firstname = $faker->firstName($gender);
            $lastname = $faker->lastName();
            $email = strtolower($firstname . '.' . $lastname . '@mail.com');
            $customer = new Customer();
            $customer
                ->setEmail($email)
                ->setLastname($lastname)
                ->setAddress($faker->address())
                ->setGender($gender)
                ->setFirstname($firstname)
                ->setPhoneNumber($faker->phoneNumber())
                ->setDateOfBirth($faker->dateTime())
                ->setUser($faker->randomElement([$admin, $user]))
                ->setCompany($faker->company())
                ->setComment($faker->text());
            $manager->persist($customer);
        }

        $manager->flush();
    }


}
