<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $code = $exception->getCode();
    
        if($exception->getCode() === 477) {
            $message = $exception->getArrayErrors();
        }

        if($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }
        
        return $this->json([
            'error'    => $message
        ],$code);
    }
}
