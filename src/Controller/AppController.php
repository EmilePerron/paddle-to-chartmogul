<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexRedirect()
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("dashboard");
        }

        return $this->redirectToRoute("login");
    }

    /**
     * @Route("/app", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('app/dashboard.html.twig');
    }

    /**
     * @Route("/app/settings", name="settings")
     */
    public function settings(Request $request, EntityManagerInterface $entityManager)
    {
		$user = $this->getUser();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($user);
			$entityManager->flush();

			$this->addFlash("success", "Your settings have been saved!");
		}

        return $this->render('app/settings.html.twig', [
			"form" => $form->createView(),
		]);
    }
}
