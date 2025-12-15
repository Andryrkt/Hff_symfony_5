<?php

namespace App\Service\Navigation\Product;

use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Service\Navigation\BreadcrumbBuilder;

class ProductBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    private $breadcrumb;

    public function __construct(BreadcrumbBuilder $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function supports(string $context): bool
    {
        return in_array($context, [
            'product_detail',
            'product_list',
            'product_category'
        ]);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'product_list';

        switch ($context) {
            case 'product_detail':
                return $this->buildProductDetail($parameters);
            case 'product_list':
                return $this->buildProductList($parameters);
            case 'product_category':
                return $this->buildProductCategory($parameters);
            default:
                throw new \InvalidArgumentException("Unsupported product context: {$context}");
        }
    }

    private function buildProductDetail(array $parameters): array
    {
        if (!isset($parameters['product_id'])) {
            throw new \InvalidArgumentException('product_id is required');
        }
        $productId = $parameters['product_id'];
        $productName = $parameters['product_name'] ?? "Produit #{$productId}";

        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'home_index')
            ->add('Produits', 'product_list')
            ->add($productName, 'product_show', ['id' => $productId])
            ->get();
    }

    private function buildProductList(array $parameters): array
    {
        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'home_index')
            ->add('Produits', 'product_list')
            ->get();
    }

    private function buildProductCategory(array $parameters): array
    {
        if (!isset($parameters['category'])) {
            throw new \InvalidArgumentException('category is required');
        }
        $category = $parameters['category'];

        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'home_index')
            ->add('Produits', 'product_list')
            ->add(ucfirst($category), 'product_category', ['category' => $category])
            ->get();
    }
}
