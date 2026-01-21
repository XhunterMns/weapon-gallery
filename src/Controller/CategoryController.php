<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class CategoryController extends AbstractController
{
     #[Route('/showallcategories', name: 'show_all_categories')]
    public function showAllCategories(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Categorie::class);
        $categories = $repo->findAll();

        return $this->render('category/showall.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/add', name:'add_category')]
    public function addCategory(Request $request, EntityManagerInterface $em): Response
    {
         $categorie = new Categorie();

        $form = $this->createFormBuilder($categorie)
            ->add('nomCategorie', TextType::class, [
                'label' => 'Nom de la catégorie',
            ])
            ->add('valider', SubmitType::class)
            ->getForm();
            
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('show_all_categories');
        }

        return $this->render('category/add_category.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/categories/{id}/edit', name: 'category_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($categorie)
            ->add('nomCategorie', TextType::class, [
                'label' => 'Nom de la catégorie',
            ])
            ->add('valider', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('show_all_categories');
        }

        return $this->render('categorie/edit.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        $token = (string) $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $token)) {
            $em->remove($categorie);
            $em->flush();
        }

        return $this->redirectToRoute('show_all_categories');
    }
}
