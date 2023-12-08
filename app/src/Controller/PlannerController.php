<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Provide a simple planner app.
 */
class PlannerController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('planner/index.html.twig', []);
    }
}
