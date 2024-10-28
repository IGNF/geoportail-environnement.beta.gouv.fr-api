<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:add',
    description: 'creates a new user',
)]
class UserAddCommand extends Command
{
    private $passwordHasher;
    private $repository;
    
    public function __construct(
        UserPasswordHasherInterface  $passwordHasher,
        UserRepository $repository
    )
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->repository = $repository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'add email')
            ->addArgument('password', InputArgument::REQUIRED, 'add password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setEmail($input->getArgument('email'));
        $user->setPassword($this->passwordHasher->hashPassword($user,$input->getArgument('password')));
        $user->setCreatedAt(new DateTimeImmutable());

        $this->repository->persist($user, true);
        
        $io->success("User is created, id = ".$user->getId().", email = " .$user->getEmail());

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose a email : ');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password : ');
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
