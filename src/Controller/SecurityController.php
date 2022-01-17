<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

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
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request)
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("dashboard");
        }

        // check if login form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Login to Paddle 2 ChartMogul',
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
}
