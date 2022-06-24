<?php

namespace App\MessageHandler;

use App\Entity\AddressStatus;
use App\Entity\RabbitTasks;
use App\Message\ToCheckMessage;
use App\Repository\RabbitTasksRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
class MessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private ManagerRegistry $doctrine,
        private RabbitTasksRepository $repository
    ) { }

    public function __invoke(ToCheckMessage $message)
    {
        $task = $message->getRelatedEntity();
        try {
            $this->updateTaskStatus($task, 'inProgress');

            $this->processMessage($message);
        } catch (\Exception $e) {
            $this->updateTaskStatus($task, 'error');
            return;
        }

        $this->updateTaskStatus($task, 'ready');
    }

    private function updateTaskStatus(RabbitTasks $task, string $taskStatus)
    {
        $entityManager = $this->doctrine->getManager();
        if (!$entityManager->isOpen()) {
            $this->doctrine->resetManager();
        }
        $task = $this->repository->find($task->getId());
        $task->setStatus($taskStatus);
        $task->setUpdatedAt(new \DateTime('now'));

        $entityManager->flush();
    }

    private function fetchAddress(string $address): int
    {
        $response = $this->client->request(
            'GET',
            $address,
        );

        return $response->getStatusCode();
    }

    private function processMessage(ToCheckMessage $message)
    {
        foreach ($message->getAddresses() as $address ) {
            $statusCode = $this->fetchAddress($address);

            $addressStatus = new AddressStatus();
            $addressStatus->setAddress($address);
            $addressStatus->setHttpStatus($statusCode);
            $addressStatus->setCratedAt(new \DateTime('now'));

            $entityManager = $this->doctrine->getManager();
            if (!$entityManager->isOpen()) {
                $this->doctrine->resetManager();
            }

            $entityManager->persist($addressStatus);
            $entityManager->flush();
        }
    }
}
