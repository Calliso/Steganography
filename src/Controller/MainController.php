<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Steganography;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    /**
     * @Route("/hide", name="hide")
     */
    public function hide(): Response
    {
        $file = 'img/mushroom.jpg';
        $message = 'HIDE BIG ELEPHANT';
        $stg = new Steganography($file, $message);
        $stg ->steganize($file, $message);
        return $this->render('main/hide.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/show", name="show")
     */
    public function show(): Response
    {
        $secretfile = 'secret.png';
        $stg = new Steganography($secretfile);
        $message = $stg->desteganize($secretfile);
        return $this->render('main/show.html.twig', [
            'message' => $message,
        ]);
    }
    /**
     * @Route("/encrypt", name="encrypt")
     */
    public function encrypt(): Response
    {
        return $this->render('main/encrypt.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
