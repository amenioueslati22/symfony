<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Search book by reference
     */
    public function searchBookByRef(string $ref): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Book[] Returns an array of Book objects ordered by author
     */
    public function booksListByAuthors(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.username', 'ASC')
            ->addOrderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Book[] Returns books published before 2023 where author has more than 10 books
     */
    public function findBooksBefore2023WithProductiveAuthors(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->andWhere('b.publicationDate < :year2023')
            ->andWhere('a.nb_books > :minBooks')
            ->setParameter('year2023', new \DateTime('2023-01-01'))
            ->setParameter('minBooks', 10)
            ->orderBy('b.publicationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Update books category from Science-Fiction to Romance
     */
    public function updateScienceFictionToRomance(): int
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction')
            ->getQuery()
            ->execute();
    }

    public function findPublishedBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->setParameter('published', true)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}