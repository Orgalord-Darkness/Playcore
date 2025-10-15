<?php

namespace App\Command;

use App\Service\MailerService;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Envoie la newsletter des jeux vidéo avec images'
)]
class SendNewsletterCommand extends Command
{
    private UserRepository $repository;
    private MailerService $mailerService;

    public function __construct(UserRepository $repository, MailerService $mailerService)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->mailerService = $mailerService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $users = $this->repository->findUsersBySubcription();

        if (empty($users)) {
            $io->warning('Aucun utilisateur trouvé.');
            return Command::SUCCESS;
        }

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        foreach ($users as $user) {
            $email = $user->getEmail();
            $this->mailerService->sendEmail(
                $user->getEmail(),
                'Newsletter automatique - Test',
                'Bonjour ' . $user->getUsername() . ', voici notre dernière newsletter.',
                'email/newsletter2.html.twig'
            );
            
            $io->text("message send to : $email");
            sleep(10);
        }

        $output->writeln('✅ Newsletter envoyée avec succès.');
        return Command::SUCCESS;
    }
}
