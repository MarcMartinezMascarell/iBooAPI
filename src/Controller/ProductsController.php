<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDoctrine()
    {
        return $this->entityManager;
    }

    //Return view with all products
    #[Route('/', name: 'products_list', methods: ['GET', 'HEAD'])]
    public function list(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('products/products.html.twig', [
            'products' => $products,
        ]);
    }

    //Retrieve all products from the database
    #[Route('api/products', name: 'products', methods: ['GET', 'HEAD'])]
    public function index(): JsonResponse
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'weight' => $product->getWeight(),
                'enabled' => $product->isEnabled(),
            ];
        }

        return new JsonResponse(['products' =>  $data], Response::HTTP_OK);
    }

    //Retrieve a product from the database
    #[Route('api/products/{id}', name: 'products_show', methods: ['GET', 'HEAD'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'weight' => $product->getWeight(),
            'enabled' => $product->isEnabled(),
        ];

        return new JsonResponse(['product' =>  $data], Response::HTTP_OK);
    }

    //Search a product from the database by name, description on id
    #[Route('api/products/search/{search}', name: 'products_search', methods: ['GET', 'HEAD'])]
    public function search(string $search): JsonResponse
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('products')
            ->from(Product::class, 'products')
            ->where('products.name LIKE :search')
            ->orWhere('products.description LIKE :search')
            ->orWhere('products.id LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        $products = $queryBuilder->getQuery()->getResult();

        if (!$products) {
            return new JsonResponse(['status' => 'No product matches the search'], Response::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'weight' => $product->getWeight(),
                'enabled' => $product->isEnabled(),
            ];
        }

        return new JsonResponse(['products' =>  $data], Response::HTTP_OK);
    }

    //Add a new product to the database from an HTTP POST request
    #[Route('api/products/create', name: 'products_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        // return $this->json(['status' => 'Product created'], Response::HTTP_CREATED);
        $entityManager = $this->entityManager;

        $product = new Product();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setWheight($request->request->get('weight'));
        $product->setEnabled($request->request->get('enabled'));

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product created'], Response::HTTP_CREATED);
    }

    //Update a product in the database from an HTTP PUT request
    #[Route('api/products/update/{id}', name: 'products_update', methods: ['PUT'])]
    public function updateProduct(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new Response(sprintf('Product %s updated', $product->getId()));
    }

    //Delete a product from the database
    #[Route('api/products/delete/{id}', name: 'products_delete', methods: ['DELETE'])]
    public function deleteProduct($id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_OK);
    }
}
