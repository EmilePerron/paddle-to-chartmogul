<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function homepage()
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("dashboard");
        }

        return $this->render('public/homepage.html.twig');
    }

    /**
     * @Route("/sitemap.xml", name="sitemap")
     */
    public function sitemap()
    {
		$routes = [
			"home",
		];

        return $this->render('public/sitemap.xml.twig', [
			"routes" => $routes,
		]);
    }
}
