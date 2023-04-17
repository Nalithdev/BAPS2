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
    #[Route('/api/categories', name: 'app_categories', methods: ['GET', 'POST'])]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $session = $this->user;

        if($session->getRoles()[0] == 'ROLE_ADMIN'){

            foreach ($categories as $c) {

                $datas = [

                    'categories' => $c,
                    'url' => '/api/categories/' . $c->getId(),

                ];

            }


        }

        return $this->json(['success' => true, 'message' => 'Vous pouvez consulter les catégories', 'categories' => $datas]);


    }

    #[Route('/api/categories/{id}', name: 'app_categories_id', methods: ['GET', 'POST'])]
    public function categoriesId(CategoryRepository $categoryRepository, $id): Response
    {
        $category = $categoryRepository->find($id);
        $session = $this->user;
        if($session->getRoles()[0] == 'ROLE_ADMIN'){

            foreach ($category as $cy) {

                $data = [

                    'id' => $cy->getId(),
                    'name' => $cy->getName(),
                    'description' => $cy->getDescription(),

                ];

            }
        }

        return $this->json(['success' => true, 'message' => 'Vous pouvez consulter les catégories', 'categories' => $data]);

    }
}
