<?php

namespace App\Controller;

use App\Entity\User;
use App\Notifier\CustomLoginLinkNotification;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login_check", name="login_check")
     */
    public function check(): void
    {
        throw new \LogicException('This code should never be reached');
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/login", name="login")
     */
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request)
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("dashboard");
        }

        // check if login form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

			if (!$user) {
				$user = (new User())
					->setEmail($email);
				$entityManager->persist($user);
				$entityManager->flush();
			}

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $notification = new CustomLoginLinkNotification(
                $loginLinkDetails,
                'Your ??? magic link ??? has arrived!',
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig');
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/settings/delete-account", name="delete_account")
     */
    public function deleteAccount(EntityManagerInterface $entityManager, RequestStack $requestStack, TokenStorageInterface $tokenStorage): Response
	{
		$user = $this->getUser();

		$tokenStorage->setToken(null);
		$requestStack->getSession()->invalidate();

		$entityManager->remove($user);
		$entityManager->flush();

		return $this->redirectToRoute("login");
	}
}
