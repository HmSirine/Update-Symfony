<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




#[AsCommand(
    name: 'CreateAdminCommand',
    description: 'Add a short description for your command',
)]
class CreateAdminCommand extends Command

{
    private $em;
    private $passwordHasher;
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
{
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }


protected function configure(): void
{
    $this
        ->setName('app:create-admin')
        ->setDescription('Créer un nouvel administrateur')
        ->setHelp('Cette commande permet de créer un nouvel administrateur.')
        ->addArgument('email', InputArgument::REQUIRED, 'Adresse email de l\'administrateur')
        ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'administrateur');
}


protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);

    // Demander l'adresse email de l'administrateur
    $email = $input->getArgument('email');
    $questionEmail = new Question(sprintf('Adresse email de l\'administrateur : '));
    $questionEmail->setValidator(function ($value) {
        if (empty($value)) {
            throw new \Exception('L\'adresse email ne peut pas être vide.');
        }

        return $value;
    });
    $email = $io->askQuestion($questionEmail);

    // Demander le mot de passe de l'administrateur
    $password = $input->getArgument('password');
    $questionPassword = new Question(sprintf('Mot de passe de l\'administrateur : '));
    $questionPassword->setHidden(true);
    $questionPassword->setValidator(function ($value) {
        if (empty($value)) {
            throw new \Exception('Le mot de passe ne peut pas être vide.');
        }

        return $value;
    });
    $password = $io->askQuestion($questionPassword);

    // Créer un nouvel utilisateur avec les rôles d'administrateur
    $admin = new Utilisateur();
    $admin->setAdresse($email);
   
    $admin->setRole('ROLE_ADMIN');
    

    // Encoder le mot de passe de l'utilisateur
    $encodedPassword = $this->passwordHasher->hashPassword($admin, $password);
    $admin->setPassword($encodedPassword);

    // Sauvegarder l'utilisateur dans la base de données
    $this->em->persist($admin);
    $this->em->flush();

    $io->success(sprintf('L\'administrateur "%s" a été créé avec succès.', $email));

    return Command::SUCCESS;
}

    
}
