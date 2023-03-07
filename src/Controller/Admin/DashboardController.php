<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Trader;
use App\Entity\User;
use App\Repository\AskRepository;
use App\Repository\ProductRepository;
use App\Repository\TraderRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'ask')]
    public function asking(AskRepository $askRepository): Response
    {

        $admin = $this->getUser();
        if ($admin){
        if ($admin->getRoles()[0] == 'ROLE_ADMIN') {
            $asks = $askRepository->findAll();
            return $this->render('admin/asking.html.twig', [
                'admin' => $admin,
                'asks' => $asks,
            ]);
        }
        }
        return $this->redirectToRoute('app_login');

    }
    #[Route('/registertrad', name: 'app_register_trad')]
    public function registertrad( Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry, TraderRepository $traderRepository, AskRepository $askRepository): Response
    {
        if ($request->get('action') == 'accept') {
            $id = $request->get('id');
            $ask = $askRepository->findOneBy($id);
            $trader = new Trader();
            $trader->setEmail($ask->getEmail());
            $trader->setPassword($ask->getPassword());
            $trader->setSiren($ask->getSiren());
            $managerRegistry->getManager()->persist($trader);

        } elseif ($request->get('action') == 'refuse') {
            $id = $request->get('id');
            $ask = $askRepository->findOneBy($id);
            $managerRegistry->getManager()->remove($ask);

        }
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('ask');
    }

    #[Route('/admin/avanced', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(AdminCrudController::class)->generateUrl());

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
            ->setTitle('Baps2');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
         yield MenuItem::linkToCrud('Commerçant', 'fas fa-list', Trader::class);
         yield MenuItem::linkToCrud('Produit', 'fas fa-list', Product::class);
    }
}
