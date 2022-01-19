<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorController extends AbstractController
{
    public function show(Throwable $exception)
    {
		if ($exception instanceof HttpException && $exception->getStatusCode() == 401) {
			return $this->redirectToRoute("login");
		}

        return $this->render('bundles/TwigBundle/Exception/error.html.twig');
    }
}
