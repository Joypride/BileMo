<?php

namespace App\Controller;

use ErrorException;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user', methods: ['GET'])]
    public function getUserList(Request $request, UserRepository $userRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {     

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "UserList" . $page . "-" . $limit;
        $userList = $cachePool->get($idCache, function (ItemInterface $item) use ($userRepository, $page, $limit) {
            $item->tag("usersCache");
            // return $userRepository->findBy(['client' => $this->getUser()]);
            $results = $userRepository->findAllWithPagination($this->getUser(), $page, $limit);
            return ['totals' => count($results), 'items' => $results->getIterator()];
        });
        // $totals = count($userList);
        // $items = $userList->getIterator();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($userList['items'], 'json', $context);
        // return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);

        return new JsonResponse(['code' => 0, 'message' => 'OK', 'items' => json_decode($jsonUserList), 'totals' => $userList['totals'], 'page' => $page, 'limit' => $limit], Response::HTTP_OK, ['accept' => 'json']);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        
            $context = SerializationContext::create()->setGroups(['getUsers']);
            $jsonUser = $serializer->serialize($user, 'json', $context);

        if ($user->getClient() == $this->getUser()) {
            return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
        }
        else {
            return new JsonResponse(['message' => 'Vous n\'avez pas la permission pour voir ce client.']);
        }

        // return new JsonResponse(['code' => 0, 'message' => 'OK', 'item' => $jsonUser], Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ClientRepository $clientRepository, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $context = DeserializationContext::create()->setGroups(['getUsers']);
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', $context);
        $user->setClient($this->getUser());

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            $context = SerializationContext::create()->setGroups(['getUsers']);
            return new JsonResponse($serializer->serialize($errors, 'json', $context), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $em->persist($user);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        
        $location = $urlGenerator->generate('app_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $em, ClientRepository $clientRepository, ValidatorInterface $validator, TagAwareCacheInterface $cache, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        if ($currentUser->getClient() == $this->getUser()) {
            $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
            $currentUser->setName($newUser->getName());
            $currentUser->setEmail($newUser->getEmail());
            $currentUser->setClient($this->getUser());
    
            // On vérifie les erreurs
            $errors = $validator->validate($currentUser);
            if ($errors->count() > 0) {
                return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
            }
    
            $em->persist($currentUser);
            $em->flush();
    
            // On vide le cache
            $cache->invalidateTags(["usersCache"]);
    
            $context = SerializationContext::create()->setGroups(['getUsers']);
            $jsonUser = $serializer->serialize($currentUser, 'json', $context);
    
            $location = $urlGenerator->generate('app_user_show', ['id' => $currentUser->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    
            return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
        }
        else {
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        if ($user->getClient() == $this->getUser()){
            $em->remove($user);
            $em->flush();

            $data = [
                'status' => 204,
                'message' => 'L\'utilisateur ' . $user->getName() . ' a bien été supprimé'
            ];

            return new JsonResponse($data, 201);
        }

        throw new ErrorException("Vous ne pouvez pas supprimer cet utilisateur");
    }
}
