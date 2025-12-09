<?php

namespace App\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Admin\PersonnelUser\User;
use Symfony\Component\Security\Core\Security;
use App\Service\Security\ContextAccessService;


trait DynamicContextFilterTrait
{
    private Security $security;
    private ContextAccessService $contextAccessService;

    // Les services seront injectés automatiquement si le trait est utilisé dans une classe avec autowiring
    public function __construct(Security $security, ContextAccessService $contextAccessService)
    {
        $this->security = $security;
        $this->contextAccessService = $contextAccessService;
    }

    /**
     * Applique un filtre dynamique basé sur le contexte de l'utilisateur
     * filtre l'agence et service Emetteur et debiteur
     */
    private function applyDynamicContextFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        $contextType, // peut être un Objet de type document ou le code de cette objet *on ne peut pas donner un type car on est dans php 7.4
        array $filterConfig,
        array $filterValues = []
    ): void {
        /** @var User $user */
        $user = $this->security->getUser();
        $context = $this->contextAccessService->getContextAccess($user, $contextType);

        foreach ($filterConfig as $config) {
            $allKey = $config['allKey'];
            $contextKey = $config['contextKey'];
            $fields = $config['fields'];
            $restrictedFields = $config['restrictedFields'] ?? [];

            if (!$context[$allKey] ?? false) {
                $orConditions = [];
                $parameters = [];

                foreach ($fields as $field) {
                    if (!in_array($field, $restrictedFields, true)) {
                        $paramName = "{$field}_filter";
                        $orConditions[] = "{$alias}.{$field} IN (:{$paramName})";
                        $parameters[$paramName] = $filterValues[$field] ?? $context[$contextKey] ?? [];
                    }
                }

                if ($orConditions !== []) {
                    $queryBuilder->andWhere($queryBuilder->expr()->orX(...$orConditions));
                    foreach ($parameters as $key => $value) {
                        $queryBuilder->setParameter($key, $value);
                    }
                }
            }
        }
    }

    private function getDocumentFilterConfig(array $restrictedAgenceFields = ['agenceDebiteurId', 'agenceEmetteurId'], array $restrictedServiceFields = ['serviceDebiteurId', 'serviceEmetteurId']): array
    {
        return [
            [
                'allKey' => 'allAgences',
                'contextKey' => 'agenceIds',
                'fields' => ['agenceDebiteurId', 'agenceEmetteurId'],
                'restrictedFields' => $restrictedAgenceFields
            ],
            [
                'allKey' => 'allServices',
                'contextKey' => 'serviceIds',
                'fields' => ['serviceDebiteurId', 'serviceEmetteurId'],
                'restrictedFields' => $restrictedServiceFields
            ]
        ];
    }
}
