<?php

namespace App\Controller;

use App\Repository\AddressStatusRepository;
use App\Repository\RabbitTasksRepository;
use App\Service\HttpCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;

final class HttpCheckController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected HttpCheckService $service,
    )
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/addresses/check', name: 'http-check', methods: "POST")]
    public function checkAddresses(Request $request): Response
    {
        $task = $this->service->processCheck(json_decode($request->getContent(), true));

        return $this->json($task);
    }

    #[Route('/tasks/index', name: 'tasks.index', methods: "GET")]
    public function tasksIndex(Request $request, RabbitTasksRepository $repository): Response
    {
        $paginationParam = $request->query->all();

        $data = $repository->allPaginate($paginationParam['page']['number'], $paginationParam['page']['size']);

        return $this->json($data);
    }

    #[Route('/addresses/result', name: 'address.result', methods: "GET")]
    public function checkResult(Request $request, AddressStatusRepository $repository): Response
    {
        $paginationParam = $request->query->all();

        $data = $repository->allPaginate($paginationParam['page']['number'], $paginationParam['page']['size']);

        return $this->json($data);
    }

    #[Route('/tasks/restart', name: 'tasks.restart', methods: "POST")]
    public function restartTasks(): Response
    {
        $this->service->restartTasks();

        return $this->json([], 200);
    }
}