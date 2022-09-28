<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('api/clients')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'client', methods: ['GET'])]
    public function getClientList(Request $request, ClientRepository $clientRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        
        $idCache = "getClientLists-" . $page . "-" . $limit;
        $clientList = $cachePool->get($idCache, function (ItemInterface $item) use ($clientRepository, $page, $limit) {
            $item->tag("clientsCache");
            return $clientRepository->findAllWithPagination($page, $limit);
        });

        $jsonClientList = $serializer->serialize($clientList, 'json', ['groups' => 'getClients']);
        return new JsonResponse($jsonClientList, Response::HTTP_OK, [], true);
    }

    // #[Route('/', name: 'client', methods: ['GET'])]
    // public function getClientList(Request $request, ClientRepository $clientRepository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    // {
    //     $page = $request->get('page', 1);
    //     $limit = $request->get('limit', 3);
        
    //     $idCache = "getClientList-" . $page . "-" . $limit;

    //     $jsonClientList = $cache->get($idCache, function (ItemInterface $item) use ($clientRepository, $page, $limit, $serializer) {
    //         $item->tag("clientsCache");
    //         $clientList = $clientRepository->findAllWithPagination($page, $limit);
    //         return $serializer->serialize($clientList, 'json', ['groups' => 'getClients']);
    //     });
      
    //     return new JsonResponse($jsonClientList, Response::HTTP_OK, []);
    // }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client, SerializerInterface $serializer): JsonResponse
    {
            $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClients']);
            return new JsonResponse($jsonClient, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
