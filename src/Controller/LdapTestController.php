<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Ldap\LdapInterface;
use Psr\Log\LoggerInterface;

class LdapTestController extends AbstractController
{
    private $ldap;
    private $logger;

    public function __construct(LdapInterface $ldap, LoggerInterface $logger)
    {
        $this->ldap = $ldap;
        $this->logger = $logger;
    }

    /**
     * @Route("/test-ldap-connection", name="test_ldap_connection")
     */
    public function testLdapConnection(): Response
    {
        $results = [];
        
        try {
            // Test 1: Vérifier les variables d'environnement
            $results['env_vars'] = [
                'LDAP_HOST' => $_ENV['LDAP_HOST'] ?? 'Non défini',
                'LDAP_PORT' => $_ENV['LDAP_PORT'] ?? 'Non défini',
                'LDAP_SEARCH_DN' => $_ENV['LDAP_SEARCH_DN'] ?? 'Non défini',
                'LDAP_BASE_DN' => $_ENV['LDAP_BASE_DN'] ?? 'Non défini',
                'LDAP_SEARCH_PASSWORD' => $_ENV['LDAP_SEARCH_PASSWORD'] ? 'Défini' : 'Non défini'
            ];

            // Test 2: Test de connexion LDAP
            $results['connection_test'] = 'Test de connexion...';
            
            // Test 3: Test de bind avec le compte technique
            $searchDn = $_ENV['LDAP_SEARCH_DN'] ?? '';
            $searchPassword = $_ENV['LDAP_SEARCH_PASSWORD'] ?? '';
            
            if (empty($searchDn) || empty($searchPassword)) {
                throw new \Exception('Variables LDAP_SEARCH_DN ou LDAP_SEARCH_PASSWORD manquantes');
            }

            $this->ldap->bind($searchDn, $searchPassword);
            $results['bind_test'] = 'Bind réussi avec le compte technique';

            // Test 4: Test de recherche d'utilisateur
            $baseDn = $_ENV['LDAP_BASE_DN'] ?? '';
            if (empty($baseDn)) {
                throw new \Exception('Variable LDAP_BASE_DN manquante');
            }

            $query = $this->ldap->query($baseDn, '(objectClass=person)');
            $searchResults = $query->execute();
            $results['search_test'] = 'Recherche réussie - ' . count($searchResults) . ' utilisateurs trouvés';

            // Test 5: Test de recherche d'un utilisateur spécifique
            $testUsername = 'test'; // Remplace par un nom d'utilisateur valide
            $userQuery = $this->ldap->query($baseDn, sprintf('(sAMAccountName=%s)', $testUsername));
            $userResults = $userQuery->execute();
            $results['user_search_test'] = 'Recherche utilisateur "' . $testUsername . '" - ' . count($userResults) . ' résultats';

            $results['status'] = 'SUCCESS';
            $results['message'] = 'Tous les tests LDAP sont passés avec succès';

        } catch (\Exception $e) {
            $results['status'] = 'ERROR';
            $results['error'] = $e->getMessage();
            $results['file'] = $e->getFile();
            $results['line'] = $e->getLine();
            
            $this->logger->error('Test LDAP échoué', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        return $this->render('ldap_test/connection.html.twig', [
            'results' => $results
        ]);
    }
} 