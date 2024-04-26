<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    /**
     * @Route("/translations/{filename}", name="load_translation")
     */
    public function loadTranslation(Request $request, $filename)
    {
        
        $translationsPath = __DIR__ . '/../../translations/';

       
        $filePath = $translationsPath . $filename;

       
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Translation file not found.');
        }

       
        $translationContent = file_get_contents($filePath);

       
        return new JsonResponse(json_decode($translationContent, true));
    }
}