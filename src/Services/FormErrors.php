<?php
declare(strict_types=1);
namespace App\Services;

use Symfony\Component\Form\FormInterface;

class FormErrors
{
    public function getFormErrors(FormInterface ...$forms) : array
    {
        $errors = [];
        foreach ($forms as $form) {
            foreach ($form->getErrors(true) as $error) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }
        }
        return $errors;
    }
}