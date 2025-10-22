<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book/add', name: 'app_book_add')]
    public function addBook(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        $book = new Book();
        $book->setPublished(true);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $book->getAuthor();
            if ($author) {
                $currentNbBooks = $author->getNbBooks();
                $author->setNbBooks($currentNbBooks + 1);
                $entityManager->persist($author);
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Book added successfully!');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/list', name: 'app_book_list')]
    public function listBooks(BookRepository $bookRepository, Request $request): Response
    {
        $searchRef = $request->query->get('search_ref');
        $books = [];

        if ($searchRef) {
            $book = $bookRepository->searchBookByRef($searchRef);
            if ($book) {
                $books = [$book];
            }
        } else {
            $books = $bookRepository->findPublishedBooks();
        }

        $unpublishedBooks = $bookRepository->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->setParameter('published', false)
            ->getQuery()
            ->getResult();

        $totalPublished = count($books);
        $totalUnpublished = count($unpublishedBooks);

        return $this->render('book/list.html.twig', [
            'books' => $books,
            'total_published' => $totalPublished,
            'total_unpublished' => $totalUnpublished,
            'search_ref' => $searchRef,
        ]);
    }

    #[Route('/books/by-authors', name: 'app_books_by_authors')]
    public function booksByAuthors(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->booksListByAuthors();

        return $this->render('book/books_by_authors.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/productive-authors', name: 'app_books_productive_authors')]
    public function booksWithProductiveAuthors(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBooksBefore2023WithProductiveAuthors();

        return $this->render('book/productive_authors.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/update-categories', name: 'app_books_update_categories')]
    public function updateCategories(BookRepository $bookRepository): Response
    {
        $updatedCount = $bookRepository->updateScienceFictionToRomance();
        
        $this->addFlash('success', sprintf('Successfully updated %d books from Science-Fiction to Romance category.', $updatedCount));
        
        return $this->redirectToRoute('app_book_list');
    }

    #[Route('/book/edit/{id}', name: 'app_book_edit')]
    public function editBook(int $id, Request $request, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Book updated successfully!');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route('/book/delete/{id}', name: 'app_book_delete')]
    public function deleteBook(int $id, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash('success', 'Book deleted successfully!');
        return $this->redirectToRoute('app_book_list');
    }

    #[Route('/book/show/{id}', name: 'app_book_show')]
    public function showBook(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}