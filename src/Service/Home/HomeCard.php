<?php

// src/Model/HomeCard.php

namespace App\Service\Home;

class HomeCard
{
    private string $title;
    private string $description;
    private string $icon;
    private string $color;
    private array $links;

    public function __construct(
        string $title,
        string $description,
        string $icon,
        string $color = 'primary',
        array $links = []
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
        $this->color = $color;
        $this->links = $links;
    }

    // Getters
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getIcon(): string
    {
        return $this->icon;
    }
    public function getColor(): string
    {
        return $this->color;
    }
    public function getLinks(): array
    {
        return $this->links;
    }
}