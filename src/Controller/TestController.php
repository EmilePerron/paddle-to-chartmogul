<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Synchronizer\Synchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(Synchronizer $synchronizer, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(2);

        $synchronizer->sync($user);

        die();
    }
}
