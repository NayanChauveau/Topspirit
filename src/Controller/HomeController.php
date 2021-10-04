<?php

namespace App\Controller;

use App\Repository\PlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PlatformRepository $platformRepo): Response
    {

        $platforms = $platformRepo->findPremiums();

        return $this->render('home/index.html.twig', [
            'platforms' => $platforms
        ]);
    }
}
