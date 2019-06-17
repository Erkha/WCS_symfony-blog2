<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use app\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{   
    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET","POST"})
     */
    public function favorite(Request $request, Article $article, ObjectManager $manager): Response
    {
        if ($this->getUser()->getFavorite()->contains($article)) {
            $this->getUser()->removeFavorite($article)   ;
        }
        else {
            $this->getUser()->addFavorite($article);
        }

        $manager->flush();

        return $this->json([
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }


    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @IsGranted("ROLE_AUTHOR",message="No access! Get out!")
     */
    public function new(Request $request,\Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $article->setSlug();
            $author = $this->getUser();
            $article->setAuthor($author);
            $entityManager->persist($article);
            $entityManager->flush();

            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
            ->setFrom($_ENV['MAILER_FROM_ADDRESS'])
            ->setTo('jmtatout@gmail.com')
            ->setBody($this->renderView('article/mailInfo.html.twig',['article'=>$article]),'text/html');
            $mailer->send($message);
            $this->addFlash('success', 'The new article has been created');
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @IsGranted("ROLE_AUTHOR",message="No access! Get out!")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_AUTHOR",message="No access! Get out!")
     */
    public function edit(Request $request, Article $article): Response
    { 
        if ($this->getUser() == $article->getAuthor()) 
        {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            $errors = [];
            if ($form->isSubmitted() && $form->isValid()) {
                $article->setSlug();
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'The  article has been updated');
                return $this->redirectToRoute('article_index', [
                    'id' => $article->getId(),
                ]);
            }

            return $this->render('article/edit.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
                'errors' => $errors
            ]);
        } 
        else
        {
            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_AUTHOR",message="No access! Get out!")
     */
    public function delete(Request $request, Article $article): Response
    {   
        if ($this->getUser() == $article->getAuthor()) {
            if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($article);
                $entityManager->flush();
                $this->addFlash('danger', 'The  article has been removed');
            }
        }
        return $this->redirectToRoute('article_index');
    }
}
