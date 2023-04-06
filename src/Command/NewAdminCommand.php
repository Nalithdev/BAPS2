<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NewAdminCommand extends Command
{
	//avec bin/console new:admin on peut créer un admin
	
	protected static $defaultName = 'new:admin';
	
	private UserPasswordHasherInterface $passwordEncoder;
	private EntityManagerInterface $entityManager;
	private $email;
	private $password;
	private $firstname;
	private $lastname;
	
	public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
	{
		parent::__construct();
		$this->passwordEncoder = $passwordEncoder;
		$this->entityManager = $entityManager;
	}
	
	protected function configure()
	{
		$this->setDescription('Create a new admin user');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->email = $this->ask($input, $output, 'Email: ');
		$this->password = $this->ask($input, $output, 'Password: ');
		$this->firstname = $this->ask($input, $output, 'Firstname: ');
		$this->lastname = $this->ask($input, $output, 'Lastname: ');
		$this->createAdmin();
		$output->writeln('Admin created');
		return Command::SUCCESS;
	}
	
	public function createAdmin()
	{
		$admin = new User();
		$admin->setEmail($this->email);
		$admin->setPassword($this->passwordEncoder->hashPassword($admin, $this->password));
		$admin->setFirstname($this->firstname);
		$admin->setLastname($this->lastname);
		$admin->setRoles(['ROLE_ADMIN']);
		$this->entityManager->persist($admin);
		$this->entityManager->flush();
	}
	
	private function ask(InputInterface $input, OutputInterface $output, string $string)
	{
		$helper = $this->getHelper('question');
		$question = new Question($string);
		return $helper->ask($input, $output, $question);
	}
	
}