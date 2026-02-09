<?php

namespace App\Command;

use App\Message\GetCurrencyMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:get-history-rates',
    description: 'Load history rates from api',
)]
class GetHistoryRatesCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::OPTIONAL, 'Num Days', 180);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = $input->getArgument('days');
        $count = 0;

        if ($days) {
            $io->note(sprintf('You passed %s days', $days));
        }

        $today = new \DateTimeImmutable('today');
        $endDate = $today->modify("-{$days} days");

        for (
            $date = $today;
            $date > $endDate;
            $date = $date->modify('-1 day')
        ) {
            $this->messageBus->dispatch(new GetCurrencyMessage($date));

            $this->logger->debug(
                'Message', [
                    'date' => $date->format('d-m-Y'),
                ]
            );

            ++$count;
        }

        $io->success(sprintf('Messages add %s to async queue.', $count));

        return Command::SUCCESS;
    }
}
