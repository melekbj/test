<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleType;
use App\Repository\ArticlesRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class BlogController extends AbstractController
{


    #[Route('/', name: 'app_blog')]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        $articles = $articlesRepository->findAll();

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAT(new DateTime());
            $article->setImage("https://picsum.photos/seed/picsum/200/300");
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_show', [ 'id' => $article->getId() ]);
        }

        return $this->render('blog/new.html.twig', [
            'form' =>$form->createView()
        ]);
    }

    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Articles $article, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_show', [ 'id' => $article->getId() ]);
        }


        return $this->render('blog/edit.html.twig', [
            'editform' =>$form->createView()
        ]); 
    }



    #[Route('/article/{id}', name: 'article_show')]
    public function show(Articles $articles): Response
    {
        return $this->render('blog/show.html.twig', [
            'article' => $articles
        ]); 
    }


 

  
    
}
