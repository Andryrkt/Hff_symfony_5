<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('base64_encode', [$this, 'base64EncodeFilter']),
        ];
    }

    public function base64EncodeFilter(string $input): string
    {
        return base64_encode($input);
    }
}
