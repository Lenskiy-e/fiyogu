<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     * @param \Throwable $exception
     * @return Response
     */
    public function response(Throwable $exception): Response
    {
        $message = $exception->getMessage();
    
        if($exception->getCode() === 477) {
            $message = $exception->getArrayErrors();
        }
        return $this->json([
            'error'    => $message
        ],$exception->getCode());
    }
}
