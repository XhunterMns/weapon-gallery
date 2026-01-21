<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


final class ArticleController extends AbstractController
{
   #[Route('/showallarticles', name: 'show_all_articles')]
    public function showAllArticles(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('article/showallArticle.html.twig', [
            'articles' => $articles,
        ]);
    }
#[Route('addarticle', name: 'add_article')]
    public function addArticle(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm("App\Form\AddArticleType",$article);
        $form -> handleRequest($request);
        if ($form->isSubmitted()) {              
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('show_all_articles');
        }
        return $this->render('article/addA.html.twig',
            ['f'=>$form->createView()]);
    }
    #[Route('/editU/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, EntityManagerInterface $em): Response
    {
        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('No article found for id ' . $id);
        }

        $fb = $this->createFormBuilder($article)
            ->add('libelle', TextType::class)
            ->add('disponile', CheckboxType::class, ['required' => false, 'label' => 'Disponible'])
            ->add('price', MoneyType::class)
            ->add('marque', TextType::class)
            ->add('imageUrl', UrlType::class, [
                'label' => 'Image URL',
                'required' => false,
                'attr' => ['class' => 'form-control'], 
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'required' => false,
            ])
            ->add('Valider', SubmitType::class);
        
        $form = $fb->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('show_all_articles');
        }
        
        return $this->render(
            'article/addA.html.twig',
            ['f' => $form->createView()]
        );
    }
    #[Route("/supp/{id}", name: "cand_delete")]
    public function delete(Request $request, $id, EntityManagerInterface $em): Response
    {
        $c = $em->getRepository(Article::class)
            ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                'No article found for id ' . $id
            );
        }
        $em->remove($c);
        $em->flush();
        return $this->redirectToRoute('show_all_articles');
}
}