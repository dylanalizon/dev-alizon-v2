<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserInitCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create an user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('admin', null, InputOption::VALUE_NONE, 'Set the user as admin'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $admin = $input->getOption('admin');

        $roles = ['ROLE_USER'];
        if ($admin) {
            $roles[] = 'ROLE_ADMIN';
        }
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
