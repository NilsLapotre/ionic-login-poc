<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $superadmin = new User();
        $superadmin->setEmail('superadmin@symfony-starter-kit.bar');
        $superadmin->setName('name');
        $superadmin->setEmail('superadmin@symfony-starter-kit.bar');
        $superadmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superadmin->setPassword(
            $this->passwordEncoder->encodePassword(
                $superadmin,
                'superadmin'
            )
        );
        $superadmin->setEnabled(true);
        $manager->persist($superadmin);

        $admin = new User();
        $admin->setEmail('admin@symfony-starter-kit.bar');
        $admin->setName('name');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordEncoder->encodePassword(
                $admin,
                'admin'
            )
        );
        $admin->setEnabled(true);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@symfony-starter-kit.bar');
        $user->setName('name');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'user'
            )
        );
        $user->setEnabled(true);
        $manager->persist($user);

        $manager->flush();
    }
}
