<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('api/products')]
class ProductController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des produits.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
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
     * @OA\Tag(name="Products")
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'product', methods: ['GET'])]
    public function getProductList(Request $request, ProductRepository $productRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        
        $idCache = "ProductLists-" . $page . "-" . $limit;
        $productList = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit) {
            $item->tag("productsCache");
            $results = $productRepository->findAllWithPagination($page, $limit);
            return ['totals' => count($results), 'items' => $results->getIterator()];
        });

        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse(['code' => 0, 'message' => 'OK', 'items' => json_decode($jsonProductList), 'totals' => $productList['totals'], 'page' => $page, 'limit' => $limit], Response::HTTP_OK, ['accept' => 'json']);
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des produits.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Tag(name="Products")
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse(['code' => 0, 'message' => 'OK', 'item' => json_decode($jsonProduct)], Response::HTTP_OK, ['accept' => 'json']);    
    }
}
