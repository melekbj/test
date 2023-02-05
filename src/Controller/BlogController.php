<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\Articles;
use App\Form\ArticleType;
use App\Form\MyCommentType;
use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class BlogController extends AbstractController
{   
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    #[Route('/', name: 'app_blog')]
    public function index(ArticlesRepository $articlesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate($articlesRepository->findAll(),
        $request->query->getInt('page', 1),
        2
        );

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, PersistenceManagerRegistry $doctrine, FlashyNotifier $flashyNotifier): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAT(new DateTime());
            $article->setImage("https://picsum.photos/seed/picsum/200/300");
            $article->setUser($this->security->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashyNotifier->success('Article added successfully', 'http://your-awesome-link.com');

            return $this->redirectToRoute('article_show', [ 'id' => $article->getId() ]);
        }
        
        return $this->render('blog/new.html.twig', [
            'form' =>$form->createView()
        ]);
    }

    

    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Articles $article, PersistenceManagerRegistry $doctrine, FlashyNotifier $flashyNotifier): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();


            $flashyNotifier->success('Article modified successfully', 'http://your-awesome-link.com');
            return $this->redirectToRoute('article_show', [ 'id' => $article->getId() ]);
        }


        return $this->render('blog/edit.html.twig', [
            'editform' =>$form->createView()
        ]); 
    }



    #[Route('/article/{id}', name: 'article_show', methods: ["GET", "POST"] )]
    public function show(Articles $articles, Request $request, PersistenceManagerRegistry $doctrine, FlashyNotifier $flashyNotifier): Response
    {
        $comment = new Comment();
        $form = $this->createForm(MyCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTime());
            $comment->setArticle($articles);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $flashyNotifier->success('Comment created successfully', 'http://your-awesome-link.com');
            return $this->redirectToRoute('article_show', [ 'id' => $articles->getId() ]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $articles,
            'commentform' => $form->createView()

        ]); 
    }


 

  
    
}
