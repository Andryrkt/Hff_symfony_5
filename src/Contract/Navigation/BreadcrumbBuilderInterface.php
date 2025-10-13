<?php

namespace App\Contract\Navigation;

interface BreadcrumbBuilderInterface
{
    public function supports(string $context): bool;
    public function build(array $parameters = []): array;
}
