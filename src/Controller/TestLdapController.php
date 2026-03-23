<?php

// src/Controller/TestLdapController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Routing\Annotation\Route;

class TestLdapController extends AbstractController
{
    #[Route('/test-ldap', name: 'test_ldap')]
    public function index(Ldap $ldap): Response
    {
        return new Response('Ldap fonctionne ✔️');
    }
}
