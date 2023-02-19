<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CategoriesController extends AbstractController
{
    private $entityManager;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    //Retrieve all categories from the database
    #[Route('api/category', name: 'categories', methods: ['GET', 'HEAD'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        //If there are no products in the database, return a 404 error
        if (!$categories) {
            return new JsonResponse(['status' => 'No categories found'], Response::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($categories as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
            ];
        }

        return new JsonResponse(['categories' =>  $data], Response::HTTP_OK);
    }

    //Add a new category to the database from an HTTP POST request
    #[Route('api/category/create', name: 'category_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {

        //Check if all mandatory parameters are present
        if(!$request->request->get('name')) {
            return new JsonResponse(['status' => 'Missing mandatory parameters'], Response::HTTP_BAD_REQUEST);
        }

        //Use the save method from the categoryRepository
        $this->categoryRepository->save($request->request->get('name'));

        return new JsonResponse(['status' => 'Category created'], Response::HTTP_CREATED);
    }
}
