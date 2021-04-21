<?php

namespace App\Command;

use App\Entity\User;
use function count;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'user:create';

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(string $name = null, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct($name);
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a user')
            ->addArgument('email', InputArgument::REQUIRED, 'email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'password of the user')
            ->addOption('is-super-admin', null, InputOption::VALUE_NONE, 'Set the super admin role or not')
            ->setHelp(<<<'EOT'
The <info>user:create</info> command creates a user:
  <info>php %command.full_name% romaric@netinfluence.ch thisIsMyPassword</info>
You can create a super admin via the is-super-admin flag:
  <info>php %command.full_name% romaric@netinfluence.ch thisIsMyPassword --is-super-admin </info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = ($input->getOption('is-super-admin')) ? ['ROLE_SUPER_ADMIN'] : ['ROLE_USER'];

        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPlainPassword($password);
        $user->setEnabled(true);

        $errors = $this->validator->validate($user, null, ['Default', 'Registration']);

        if (count($errors) <= 0) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success('Your user is created !');

            return 0;
        } else {
            $io->error((string) $errors);

            return 1;
        }
    }
}
