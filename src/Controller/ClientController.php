<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/clients')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'client', methods: ['GET'])]
    public function getClientList(Request $request, ClientRepository $clientRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $clientList = $clientRepository->findAll();

        $jsonClientList = $serializer->serialize($clientList, 'json', ['groups' => 'getClients']);
        return new JsonResponse($jsonClientList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client, SerializerInterface $serializer): JsonResponse
    {
            $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClients']);
            return new JsonResponse($jsonClient, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un client')]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json', ['groups' => 'getClients']);

        // On vérifie les erreurs
        $errors = $validator->validate($client);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json', ['groups' => 'getClients']), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($client);
        $em->flush();

        $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClients']);
        
        $location = $urlGenerator->generate('app_client_show', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['PUT'])]
    public function edit(Request $request, SerializerInterface $serializer, Client $currentClient, EntityManagerInterface $em): JsonResponse
    {
        $updatedClient = $serializer->deserialize($request->getContent(), 
                Client::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentClient]);
        
        $em->persist($updatedClient);
        $em->flush();
        
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['DELETE'])]
    public function delete(Client $client, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($client);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
