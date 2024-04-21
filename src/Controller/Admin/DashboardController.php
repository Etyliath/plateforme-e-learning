<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\Programme;
use App\Entity\Theme;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Plateforme E Learning');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('HomePage','fa fa-home',$this->generateUrl('home.index'));
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-gauge');
        yield MenuItem::linkToCrud('Theme','fa fa-layer-group',Theme::class);
        yield MenuItem::linkToCrud('Programme','fa fa-graduation-cap',Programme::class);
        yield MenuItem::linkToCrud('Lesson','fa fa-book-open-reader',Lesson::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
    }
}
