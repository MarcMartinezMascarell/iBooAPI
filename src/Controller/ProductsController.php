<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private $entityManager;
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    //Return view with all products
    #[Route('/', name: 'products_list', methods: ['GET', 'HEAD'])]
    public function list(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('products/products.html.twig', [
            'products' => $products,
        ]);
    }

    //Retrieve all products from the database
    #[Route('api/products', name: 'products', methods: ['GET', 'HEAD'])]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();

        //If there are no products in the database, return a 404 error
        if (!$products) {
            return new JsonResponse(['status' => 'No products found'], Response::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'weight' => $product->getWeight(),
                'enabled' => $product->isEnabled(),
                'img' => $product->getImg(),
                'category' => $product->getCategory()->getName(),
            ];
        }

        return new JsonResponse(['products' =>  $data], Response::HTTP_OK);
    }

    //Retrieve a product from the database
    #[Route('api/products/{id}', name: 'products_show', methods: ['GET', 'HEAD'])]
    public function show(int $id): JsonResponse
    {

        $product = $this->productRepository->find($id);

        //If there is no product with the given id, return a 404 error
        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        //Use the toArray method to convert the product to an array
        $data = $product->toArray();

        return new JsonResponse(['product' =>  $data], Response::HTTP_OK);
    }

    //Search a product from the database by name, description on id
    #[Route('api/products/search/{search}', name: 'products_search', methods: ['GET', 'HEAD'])]
    public function search(string $search): JsonResponse
    {

        //Use the search method from the ProductRepository to search for products that contains the search string in their name, description or id
        $products = $this->productRepository->search($search);

        //If no product matches the search, return a 404 error
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
                'img' => $product->getImg(),
                'category' => $product->getCategory()->getName(),
            ];
        }

        return new JsonResponse(['products' =>  $data], Response::HTTP_OK);
    }

    //Add a new product to the database from an HTTP POST request
    #[Route('api/products/create', name: 'products_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {

        //Check if all mandatory parameters are present
        if(!$request->request->get('name') || !$request->request->get('description') || !$request->request->get('weight') ||
            !$request->request->get('category')) {
            return new JsonResponse(['status' => 'Missing mandatory parameters'], Response::HTTP_BAD_REQUEST);
        }

        //Use the save method from the ProductRepository
        $product = $this->productRepository->save($request->request->get('name'), $request->request->get('description'),
                                        $request->request->get('weight'), $request->request->get('enabled'), $request->request->get('image_url'),
                                        (int)$request->request->get('category'));

        return new JsonResponse(['status' => 'Product created', 'product' => $product->toArray()], Response::HTTP_CREATED);
    }

    //Update a product in the database from an HTTP PUT request
    #[Route('api/products/update/{id}', name: 'products_update', methods: ['PATCH'])]
    public function updateProduct(Request $request, $id): JsonResponse
    {

        $product = $this->productRepository->find($id);

        //If there is no product with the given id, return a 404 error
        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        //Update only the fields that are present in the request
        if ($request->request->get('name')) {
            $product->setName($request->request->get('name'));
        }
        if ($request->request->get('description')) {
            $product->setDescription($request->request->get('description'));
        }
        if ($request->request->get('weight')) {
            $product->setWeight($request->request->get('weight'));
        }
        if ($request->request->get('enabled')) {
            $product->setEnabled(true);
        } else if($request->request->get('enabled') == '0') {
            $product->setEnabled(false);
        }
        if ($request->request->get('img')) {
            $product->setImg($request->request->get('img'));
        }
        if ($request->request->get('category')) {
            $product->setCategory($request->request->get('category'));
        }

        //Use the update method from the ProductRepository
        $product = $this->productRepository->update($product);


        return new JsonResponse(['status' => 'Product updated', 'product' => $product->toArray()], Response::HTTP_OK);
    }

    //Delete a product from the database
    #[Route('api/products/delete/{id}', name: 'products_delete', methods: ['DELETE'])]
    public function deleteProduct($id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        //If there is no product with the given id, return a 404 error
        if (!$product) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        //Use the remove method from the ProductRepository
        $this->productRepository->remove($product);

        return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_OK);
    }
}
