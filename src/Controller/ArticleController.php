<?php

namespace App\Controller;

use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\Form\Extenstion\Core\Type\TextType;
// use Symfony\Component\Form\Extenstion\Core\Type\TextAreaType;
// use Symfony\Component\Form\Extenstion\Core\Type\SubmitType;

class ArticleController extends AbstractController {

    /**
     * @Route("/", name="article_list")
     */
    public function index() {

        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * @Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('body', TextAreaType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * @Method({"UPDATE"})
     */
    public function edit(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('body', TextAreaType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/article/delete/{id}", name="delete_article")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/article/save")
     */
    public function save() {
        // $entityManager = $this->getDoctrine()->getManager();

        // $article = new Article();
        // $article->setTitle('Article two');
        // $article->setBody('This is the body for article 2');

        // $entityManager->persist($article);

        // $entityManager->flush();

        // return new Response('Article saved with the id of ' . $article->getId());


    }

}