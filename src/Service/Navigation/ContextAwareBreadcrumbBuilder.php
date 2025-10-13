<?php

namespace App\Service\Navigation;

class ContextAwareBreadcrumbBuilder
{
    private iterable $builders;
    /**
     * @param iterable<BreadcrumbBuilderInterface> $builders
     */
    public function __construct(
        iterable $builders
    ) {
        $this->builders = $builders;
    }

    public function build(string $context, array $parameters = []): array
    {
        foreach ($this->builders as $builder) {
            if ($builder->supports($context)) {
                return $builder->build(array_merge($parameters, ['context' => $context]));
            }
        }

        throw new \RuntimeException("No breadcrumb builder found for context: {$context}");
    }

    /**
     * Méthode utilitaire pour les contexts courants
     */
    public function productDetail(int $productId, ?string $productName = null): array
    {
        $parameters = ['product_id' => $productId];
        if ($productName) {
            $parameters['product_name'] = $productName;
        }

        return $this->build('product_detail', $parameters);
    }

    public function userProfile(int $userId, ?string $userName = null): array
    {
        $parameters = ['user_id' => $userId];
        if ($userName) {
            $parameters['user_name'] = $userName;
        }

        return $this->build('user_profile', $parameters);
    }

    public function adminSection(string $section): array
    {
        return $this->build('admin_section', ['section' => $section]);
    }
}
