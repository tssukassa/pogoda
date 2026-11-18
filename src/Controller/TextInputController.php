<?php

namespace App\Controller;

use App\Form\TextInputType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TextInputController extends AbstractController
{
    #[Route('/text_input', name: 'app_text_input', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TextInputType::class);
        $form->handleRequest($request);

        $submittedText = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $submittedText = $formData['text'] ?? null;
        }

        return $this->render('text_input/index.html.twig', [
            'form' => $form,
            'submittedText' => $submittedText,
        ]);
    }
}
