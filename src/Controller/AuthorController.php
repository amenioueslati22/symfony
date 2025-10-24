<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Service\HappyQuote;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
      
    #[Route('/authorName/{name}', name: 'showAuthor')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'nom' => $name,
        ]);
    }
    
    #[Route('/afficher', name: 'afficher')]
    public function Afficher(): Response
    {
        return new Response('hello yuna');
    }

    #[Route('/listAuthors', name: 'listAuthors')]
    public function ListAuthors(): Response
    { 
        $authors = array(
            array(
                'id' => 1,
                'picture' => '/assets/images/Victor-Hugo.jpg',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100
            ),
            array(
                'id' => 2,
                'picture' => '/assets/images/catt.jpg',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200
            ),
            array(
                'id' => 3,
                'picture' => '/assets/images/Taha_Hussein.jpg',
                'username' => 'Taha Hussein',
                'email' => 'taha.hussein@gmail.com',
                'nb_books' => 300
            ),
        );

        return $this->render('author/list.html.twig', [
            'authors' => $authors
        ]);
    }
    
    #[Route('/author/details/{id}', name: 'author_details')]
    public function authorDetails(int $id): Response
    {
        $authors = [
            [
                'id' => 1,
                'picture' => '/assets/images/Victor-Hugo.jpg',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100
            ],
            [
                'id' => 2,
                'picture' => '/assets/images/catt.jpg',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200
            ],
            [
                'id' => 3,
                'picture' => '/assets/images/Taha_Hussein.jpg',
                'username' => 'Taha Hussein',
                'email' => 'taha.hussein@gmail.com',
                'nb_books' => 300
            ],
        ];

        $author = null;
        foreach ($authors as $a) {
            if ($a['id'] == $id) {
                $author = $a;
                break;
            }
        }

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }
    
    #[Route('/showAllAuthors', name: 'show_all_authors')]
public function ShowAllAuthors(AuthorRepository $repo, HappyQuote $quote): Response
{
    $bestAuthorMessage = $quote->getHappyMessage();
    $authors = $repo->findAll();
    
    return $this->render('author/listAuthor.html.twig', [
        'list' => $authors, 
        'theBest' => $bestAuthorMessage 
    ]);
}

    #[Route('/authors/by-email', name: 'app_authors_by_email')]
    public function listAuthorsByEmail(AuthorRepository $repo): Response
    {
        $authors = $repo->listAuthorByEmail();
        
        return $this->render('author/list_by_email.html.twig', [
            'authors' => $authors,
        ]);
    }
    
    #[Route('/add', name: 'add_author')]
    public function Add(ManagerRegistry $doctrine): Response
    {
        $author = new Author();
        $author->setUsername('Test');
        $author->setEmail('test@esprit.tn');
        $author->setAge(25);
        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();
        return new Response('Author add successfully');
    }
    
    #[Route('/addForm', name: 'add_author_form')]
    public function addForm(ManagerRegistry $doctrine, Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Add', SubmitType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();
            
            $this->addFlash('success', 'Author added successfully!');
            return $this->redirectToRoute('show_all_authors');
        }

        return $this->render('author/add.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_author')]
    public function editAuthor(ManagerRegistry $doctrine, Request $request, $id, AuthorRepository $repo): Response
    {
        $author = $repo->find($id);
        
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }
        
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Update', SubmitType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            
            $this->addFlash('success', 'Author updated successfully!');
            return $this->redirectToRoute('show_all_authors');
        }

        return $this->render('author/edit.html.twig', [
            'formulaire' => $form->createView(),
            'author' => $author
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_author')]
    public function Delete(ManagerRegistry $doctrine, $id, AuthorRepository $repo): Response
    {
        $author = $repo->find($id);
        
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }
        
        $em = $doctrine->getManager();
        $em->remove($author);
        $em->flush();
        
        $this->addFlash('success', 'Author deleted successfully!');
        return $this->redirectToRoute('show_all_authors');
    }
    
    #[Route('/showDetailsAuthor/{id}', name: 'show_author_details')]
    public function showDetailsAuthor(AuthorRepository $repo, $id): Response
    {
        $author = $repo->find($id);
        
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }
        
        return $this->render('author/ShowDetailsAuthor.html.twig', ['author' => $author]);
    }
   
}