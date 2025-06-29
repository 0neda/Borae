<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route(path: '/entrar', name: 'borae_entrar')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $erro = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $ultimoUsuario = $authenticationUtils->getLastUsername();

        if ($erro) {
            $this->addFlash('erro', 'Credenciais inválidas. Verifique seu email e senha.');
        }

        return $this->render('auth/entrar.html.twig', [
            'ultimo_usuario' => $ultimoUsuario,
            'erro' => $erro,
        ]);
    }

    #[Route(path: '/sair', name: 'borae_sair')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
