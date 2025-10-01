<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
      
    #[Route('/authorName/{name}', name: 'showAuthor')]
    public function showAuthor( $name): Response
    {
        return $this->render('author/show.html.twig', [
            'nom' => $name,
        ]);
    }
    #[Route('/afficher', name: 'afficher')]
    public function Afficher( ): Response
    {
     return new Response ('hello yuna');
    }

    
      #[Route('/listAuthors', name: 'listAuthors')]
    public function ListAuthors( ): Response
    { $authors = array(
            array(
                'id' => 1,
                'picture' => 'assets/images/Victor-Hugo.jpg',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100
            ),
            array(
                'id' => 2,
                'picture' => 'assets/images/catt.jpg',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200
            ),
            array(
                'id' => 3,
                'picture' => 'assets/images/Taha_Hussein.jpg',
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
                'picture' => 'assets/images/Victor-Hugo.jpg',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100
            ],
            [
                'id' => 2,
                'picture' => 'assets/images/catt.jpg',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200
            ],
            [
                'id' => 3,
                'picture' => 'assets/images/Taha_Hussein.jpg',
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
}
