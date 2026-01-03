<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESPONSABLE')]
final class AdminController extends AbstractController
{
    #[Route('/admin/legacy', name: 'app_admin')]
    public function index(): Response
    {
        // Redirect to EasyAdmin dashboard
        return $this->redirectToRoute('admin');
    }
}
