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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('api/users')]
class UserController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des utilisateurs.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'user', methods: ['GET'])]
    public function getUserList(Request $request, UserRepository $userRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {     
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "UserList" . $page . "-" . $limit;
        $userList = $cachePool->get($idCache, function (ItemInterface $item) use ($userRepository, $page, $limit) {
            $item->tag("usersCache");
            $results = $userRepository->findAllWithPagination($this->getUser(), $page, $limit);
            return ['totals' => count($results), 'items' => $results->getIterator()];
        });

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($userList['items'], 'json', $context);

        return new JsonResponse(['code' => 0, 'message' => 'OK', 'items' => json_decode($jsonUserList), 'totals' => $userList['totals'], 'page' => $page, 'limit' => $limit], Response::HTTP_OK, ['accept' => 'json']);
    }

    /**
     * Cette méthode permet de récupérer le détail d'un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
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

        return new JsonResponse(['code' => 0, 'message' => 'OK', 'item' => json_decode($jsonUser)], Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * Cette méthode permet de créer un nouvel utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne le nouvel utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/new', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ClientRepository $clientRepository, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $context = DeserializationContext::create()->setGroups(['getUsers']);
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', $context);
        $user->setClient($this->getUser());

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            $context = SerializationContext::create()->setGroups(['getUsers']);
            return new JsonResponse($serializer->serialize($errors, 'json', $context), JsonResponse::HTTP_BAD_REQUEST, []);
        }
        
        $em->persist($user);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        
        $location = $urlGenerator->generate('app_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(['code' => 0, 'message' => 'OK', 'item' => json_decode($jsonUser)], Response::HTTP_CREATED, ["Location" => $location]);
    }

    /**
     * Cette méthode permet de modifier un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne les nouvelles informations de l'utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
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
                return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST);
            }
    
            $em->persist($currentUser);
            $em->flush();
    
            // On vide le cache
            $cache->invalidateTags(["usersCache"]);
    
            $context = SerializationContext::create()->setGroups(['getUsers']);
            $jsonUser = $serializer->serialize($currentUser, 'json', $context);
    
            $location = $urlGenerator->generate('app_user_show', ['id' => $currentUser->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    
            return new JsonResponse(['code' => 0, 'message' => 'OK', 'item' => json_decode($jsonUser)], Response::HTTP_CREATED, ["Location" => $location]);
        }
        else {
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }
    }

    /**
     * Cette méthode permet de supprimer un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="OK",
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
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
