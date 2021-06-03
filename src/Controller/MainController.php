<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Steganography;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    public function hide(Request $request): Response
    {
        $defaultData = ['message' => 'Message to encode'];
        $form = $this->createFormBuilder($defaultData)
            ->add('message', TextType::class)
            ->add('Send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $check = "Nie zapisano wiadomosci do zakodowania.";
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $check = "Wiadomosc zapisana";
        }

        // ... render the form
        $file = 'img/mushroom.jpg';
        $message = $data['message'];
        $stg = new Steganography($file, $message);
        $stg ->steganize($file, $message);
        return $this->render('main/hide.html.twig', [
            'controller_name' => 'MainController',
            'form' => $form->createView(),
            'mess' => $message,
            'check' => $check,
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
