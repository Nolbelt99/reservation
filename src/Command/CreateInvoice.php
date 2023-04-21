<?php

namespace App\Command;

use DateTime;
use App\Entity\Receipt;
use App\Entity\Transaction;
use Psr\Log\LoggerInterface;
use App\Service\SzamlazzService;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CreateInvoice extends Command
{
    protected static $defaultName = 'app:create-invoice';
    private SzamlazzService $szamlazzService;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private $projectDir;
    private ParameterBagInterface $params;
    private LoggerInterface $logger;


    public function __construct(
        SzamlazzService $szamlazzService,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        $projectDir,
        ParameterBagInterface $params,
        LoggerInterface $logger)
    {
        $this->szamlazzService = $szamlazzService;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->projectDir = $projectDir;
        $this->params = $params;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $transactions = $this->entityManager->getRepository(Transaction::class)->findAllSuccesfulWithoutReceipt();
            $count = 0;

            foreach ($transactions as $transaction) {
                $user = $transaction->getUser();
                $agentApiKey = null;
                foreach (json_decode($this->params->get('company_data')) as $company) {
                    if ($company->name == $transaction->getPaymentItem()->getCompanyName()) {
                        $agentApiKey = $company->agent_api_key;
                    }
                }
                $invoiceNumber = $this->szamlazzService->crateInvoive($user, $transaction, $agentApiKey);
                if ($invoiceNumber != 'Hiba') {
                    $receipt = new Receipt();
                    $receipt->setTransaction($transaction);
                    $receipt->setReservation($transaction->getPaymentItem()->getReservation());
                    $receipt->setIdentifier($invoiceNumber);
                    $receipt->setUser($user);
                    $receipt->setCreatedAt(new DateTime());
                    $transaction->setHasReceipt(true);
    
                    $this->entityManager->persist($transaction);
                    $this->entityManager->persist($receipt);
                    $count++;

                    $this->szamlazzService->setInvoicePaid($invoiceNumber, $transaction->getPaymentItem()->getSumPrice(), $agentApiKey);
                    $this->szamlazzService->download($invoiceNumber, $agentApiKey);

                    try {
                        $email = (new Email())
                            ->from($this->params->get('mailer_from_address'))
                            ->to($user->getEmail())
                            ->subject('Számla')
                            ->attachFromPath($this->projectDir . '/src/SzamlaAgent/Files/pdf/'.$invoiceNumber.'.pdf', $invoiceNumber)
                        ;
                        $this->mailer->send($email);
                    } catch (\Exception $e) {
                        $output->writeln('Hiba a számla e-mail küldésben.' . $e->getMessage());
                    }
                } else {
                    $this->logger->error($user->getEmail() . ' számlájának létehozásában hiba történt.');
                }
            }
    
            $this->entityManager->flush();
            $output->writeln('Sikeres számlakészítés. ' . $count . ' új számla jött létre.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Hiba a számlakészítés közben' . $e);
            return Command::FAILURE;
        }
    }
}