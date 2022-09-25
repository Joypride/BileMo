<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
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

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'product', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findAll();
        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, SerializerInterface $serializer): JsonResponse
    {
            $jsonProduct = $serializer->serialize($product, 'json');
            return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($product);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($product);
        $em->flush();

        $jsonProduct = $serializer->serialize($product, 'json');
        
        $location = $urlGenerator->generate('app_product_show', ['id' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['PUT'])]
    public function edit(Request $request, SerializerInterface $serializer, Product $currentProduct, EntityManagerInterface $em): JsonResponse
    {
        $updatedProduct = $serializer->deserialize($request->getContent(), 
                Product::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProduct]);
        
        $em->persist($updatedProduct);
        $em->flush();
        
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($product);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
