<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use http\Env\Request;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/categories', name: 'app_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->json(['success' => false, 'message' => 'Vous pouvez consulter les catégories', 'categories' => $categories]);


    }

    #[Route('/admin/categories/add', name: 'app_categories')]
    public function addcategories(ManagerRegistry $managerRegistry): Response
    {
        $categories = new Category();
        $categories->setName('test');
        $categories->setDescription('description test');


        $data = [
            'Nom de la categorie' => $categories->getName(),
        ];

        $managerRegistry->getManager()->persist($data);
        $managerRegistry->getManager()->flush();

        return $this->json(['success' => false, 'message' => 'Vous pouvez consulter les catégories', 'categories' => $data]);


    }
}
