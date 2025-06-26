<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/toggle-admin', name: 'app_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        /** @var Usuario $user */
        $user = $this->getUser();

        $user->toggleAdmin();
        $entityManager->flush();

        $this->atualizarSessaoUsuario($user, $tokenStorage);

        $message = $user->isAdmin() ? 'Você agora é administrador!' : 'Privilégios de admin removidos!';
        $category = $user->isAdmin() ? 'success' : 'error';
        $this->addFlash($category, $message);

        return $this->redirectToRoute('app_index');
    }

    #[Route('/admin/toggle-empresa', name: 'app_toggle_empresa', methods: ['POST'])]
    public function toggleEmpresa(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        /** @var Usuario $user */
        $user = $this->getUser();

        $user->ToggleEmpresa();
        $entityManager->flush();

        $this->atualizarSessaoUsuario($user, $tokenStorage);

        $message = $user->isEmpresa() ? 'Você agora é uma empresa!' : 'Privilégios de empresa removidos!';
        $category = $user->isEmpresa() ? 'success' : 'error';
        $this->addFlash($category, $message);

        return $this->redirectToRoute('app_index');
    }

    private function atualizarSessaoUsuario(Usuario $user, TokenStorageInterface $tokenStorage): void
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $newToken = new UsernamePasswordToken(
                $user,
                'main',
                $user->getRoles()
            );
            $tokenStorage->setToken($newToken);
        }
    }
}
