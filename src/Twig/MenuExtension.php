<?php

// src/Twig/MenuExtension.php
namespace App\Twig;

use App\Service\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class MenuExtension extends AbstractExtension implements GlobalsInterface
{
    private $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function getGlobals(): array
    {
        return [
            'mainMenu' => $this->menuBuilder->getMainMenu(),
        ];
    }
}
