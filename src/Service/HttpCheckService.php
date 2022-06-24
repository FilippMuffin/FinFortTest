<?php

namespace App\Service;

use App\Entity\RabbitTasks;
use App\Message\ToCheckMessage;
use App\Repository\RabbitTasksRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\MessageBusInterface;

class HttpCheckService
{
    public function __construct(
        protected MessageBusInterface $bus,
        protected ManagerRegistry     $doctrine,
        protected RabbitTasksRepository $rabbitTasksRepository
    )
    {
    }

    public function processCheck(array $addresses): RabbitTasks
    {
        $message = new ToCheckMessage($addresses);
        $this->saveTask($message);
        $this->bus->dispatch($message);

        return $message->getRelatedEntity();
    }

    public function restartTasks(): void
    {
        $tasks = $this->rabbitTasksRepository->findAll();
        foreach ($tasks as $task) {
            $message = new ToCheckMessage($task->getAddresses(), $task);
            $this->updateTaskStatus($task);
            $this->bus->dispatch($message);
        }
    }

    private function updateTaskStatus(RabbitTasks $task)
    {
        $entityManager = $this->doctrine->getManager();
        $task->setStatus('pending');
        $task->setUpdatedAt(new \DateTime('now'));

        $entityManager->flush();
    }

    private function saveTask(ToCheckMessage &$message)
    {
        $entityManager = $this->doctrine->getManager();

        $task = new RabbitTasks();
        $task->setStatus('pending');
        $task->setAddresses($message->getAddresses());
        $task->setCratedAt(new \DateTime('now'));
        $task->setUpdatedAt(new \DateTime('now'));

        $entityManager->persist($task);
        $entityManager->flush();
        $message->setRelatedEntity($task);
    }

}
