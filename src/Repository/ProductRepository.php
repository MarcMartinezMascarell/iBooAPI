<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Product::class);
        $this->entityManager = $entityManager;
    }

    public function save($name, $description, $weight, $enabled, $img, $categoryId): void
    {
        $product = new Product();
        $product->setName($name);
        $product->setDescription($description);
        $product->setWeight($weight);
        $product->setEnabled($enabled);
        if($img) {
            $product->setImg($img);
        }
        $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
        $product->setCategory($category);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function update(Product $product): Product
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return $product;
    }

    public function remove(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

   /**
    * @return Product[] Returns an array of Product objects
    */
   public function search($search): array
   {

        //Search for products with a name, description or id that contains the search string
        return $this->createQueryBuilder('products')
            ->where('products.name LIKE :search')
            ->orWhere('products.description LIKE :search')
            ->orWhere('products.id LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();

   }

   /**
    * @return Product[] Returns an array of Product objects
    */
    public function findAllEnabled(): array {
        return $this->createQueryBuilder('p')
        ->where('p.enabled = :enabled')
        ->setParameter('enabled', true)
        ->getQuery()
        ->getResult();
    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
