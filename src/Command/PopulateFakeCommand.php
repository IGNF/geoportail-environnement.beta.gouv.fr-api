<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Foret;
use App\Repository\ForetRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:populate-fake',
    description: 'Add a short description for your command',
)]
class PopulateFakeCommand extends Command
{
    private $passwordHasher;
    private $userRepository;
    private $foretRepository;

    public function __construct(
        UserPasswordHasherInterface  $passwordHasher,
        UserRepository $userRepository,
        ForetRepository $foretRepository
    )
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->foretRepository = $foretRepository;
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user1 = new User();
        $user1->setEmail('david@ign');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'david'));
        $this->userRepository->persist($user1, true);

        $user2 = new User();
        $user2->setEmail('clement@ign');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'clement'));
        $this->userRepository->persist($user2, true);

        $user3 = new User();
        $user3->setEmail('manu@ign');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'manu'));
        $this->userRepository->persist($user3, true);

        $user4 = new User();
        $user4->setEmail('daniel@ign');
        $user4->setPassword($this->passwordHasher->hashPassword($user4, 'daniel'));
        $this->userRepository->persist($user4, true);

        $foret1 = (new Foret())
            ->setArea(25)
            ->setImageUrl('https://prod-printler-front-as.azurewebsites.net/media/photo/152470.jpg?mode=crop&width=725&height=1024&rnd=0.0.1')
            ->setName('Foret 1 de Manu')
            ->setOwner($user3)
            ->setParcels(['parcel 1', 'parcel 2'])
            ->setTags(['tag1', 'tag2'])
            ->setGeometry('MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))')
        ;
        $this->foretRepository->persist($foret1, true);
        
        $foret2 = (new Foret())
            ->setArea(25)
            ->setImageUrl('https://images.rtl.fr/~c/770v513/rtl/www/1315993-baby-groot-dans-les-gardiens-de-la-galaxie-2.jpg')
            ->setName('Foret 2 de Manu')
            ->setOwner($user3)
            ->setParcels(['parcel 1', 'parcel 2'])
            ->setTags(['tag1'])
            ->setGeometry('MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))')
        ;
        $this->foretRepository->persist($foret2, true);
        
        $foret3 = (new Foret())
            ->setArea(25)
            ->setImageUrl('https://images.rtl.fr/~c/770v513/rtl/www/1315993-baby-groot-dans-les-gardiens-de-la-galaxie-2.jpg')
            ->setName('Foret de Clément')
            ->setOwner($user2)
            ->setParcels(['parcel 1', 'parcel 2'])
            ->setTags(['tag1'])
            ->setGeometry('MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))')
        ;
        $this->foretRepository->persist($foret3, true);
        
        $foret4 = (new Foret())
            ->setArea(25)
            ->setImageUrl('https://images.rtl.fr/~c/770v513/rtl/www/1315993-baby-groot-dans-les-gardiens-de-la-galaxie-2.jpg')
            ->setName('Foret de David')
            ->setOwner($user1)
            ->setParcels(['parcel 1', 'parcel 2'])
            ->setTags(['tag1'])
            ->setGeometry('MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))')
        ;
        $this->foretRepository->persist($foret4, true);

        $foret5 = (new Foret())
            ->setArea(25)
            ->setImageUrl('https://images.rtl.fr/~c/770v513/rtl/www/1315993-baby-groot-dans-les-gardiens-de-la-galaxie-2.jpg')
            ->setName('Foret de Daniel')
            ->setOwner($user4)
            ->setParcels(['parcel 1', 'parcel 2'])
            ->setTags(['tag1'])
            ->setGeometry('MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))')
        ;
        $this->foretRepository->persist($foret5, true);

        $io->success("done");
        
        return Command::SUCCESS;
    }
}
