<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Commerce;
use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
    #[Route('/admin', name: 'ask')]
    public function asking(UserRepository $userRepository): Response
    {

        $admin = $this->getUser();
        if ($admin){
            if ($admin->getRoles()[0] == 'ROLE_ADMIN') {
                $asks = $userRepository->findBy(
                    [
                        'approved'=> '0',

                        ] );

                return $this->render('admin/asking.html.twig', [
                    'admin' => $admin,
                    'asks' => $asks,
                ]);
            }
        }
        return $this->redirectToRoute('app_login');

    }
    #[Route('/registertrad', name: 'app_register_trad')]
    public function registertrad( Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry, UserRepository $userRepository): Response
    {
        if ($request->get('action') == 'accept') {
            $id = $request->get('id');
            $user = $userRepository->findOneBy(['id' => $id]);

            $user->setApproved(true);
            $managerRegistry->getManager()->persist($user);

        } elseif ($request->get('action') == 'refuse') {
            $id = $request->get('id');
            $user = $userRepository->findOneBy(['id'=> $id]);
            $managerRegistry->getManager()->remove($user);

        }
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('ask');
    }

	#[Route('/admin/advanced', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration Avancée');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToUrl('Retour au site', 'fas fa-home', '/');
        yield MenuItem::linkToUrl("Administration", 'fas fa-gear', '/admin');
		yield MenuItem::linkToUrl("Une otarie qui tourne", 'fas fa-water', 'https://www.youtube.com/watch?v=eY52Zsg-KVI');
		//ajouter un espace
		yield MenuItem::section('Base de données');
		yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
		yield MenuItem::linkToCrud('Commerces', 'fas fa-shopping-cart', Commerce::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-shopping-cart', Category::class);

    }
}
