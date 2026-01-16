<?php

namespace App\Factory\Hf\Atelier\Dit;

use App\Dto\Hf\Atelier\Dit\FormDto;

class FormFactory
{
    public function create(): FormDto
    {
        return new FormDto();
    }
}
