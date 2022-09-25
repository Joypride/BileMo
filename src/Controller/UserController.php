<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAll();
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
            $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
            return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'getUsers']);

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json', ['groups' => 'getUsers']), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        
        $location = $urlGenerator->generate('app_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $em): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(), 
                User::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        
        $em->persist($updatedUser);
        $em->flush();
        
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
