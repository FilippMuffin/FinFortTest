<?php

namespace App\Tests;

use App\Entity\RabbitTasks;
use App\Message\ToCheckMessage;
use App\MessageHandler\MessageHandler;
use App\Repository\RabbitTasksRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpCheckConsumer extends KernelTestCase
{
    public function testMessageHandler()
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var RabbitTasksRepository $repository */
        $repository = $container->get(RabbitTasksRepository::class);
        $task = new RabbitTasks();
        $task->setAddresses(["https://google.com"])
            ->setStatus('pending')
            ->setCratedAt(new \DateTime('now'))
            ->setUpdatedAt(new \DateTime('now'));
        $repository->add($task, true);

        $message = new ToCheckMessage([
            "https://google.com"
        ], $task);

        /** @var MessageHandler $handler */
        $handler = $container->get(MessageHandler::class);
        $result = $handler($message);

        $this->assertEquals($result, 0);
    }
}