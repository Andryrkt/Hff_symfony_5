<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use Symfony\Component\Form\FormInterface;

class CreationHandler
{
    public function handel(FormInterface $form)
    {
        $dto = $form->getData();
    }
}
