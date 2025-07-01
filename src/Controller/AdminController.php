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
    #[Route('/admin', name: 'borae_admin')]
    public function index(\App\Repository\UsuarioRepository $usuarioRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usuarios = $usuarioRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/admin/toggle-admin/{id}', name: 'borae_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(int $id, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
        }
        $usuario->toggleAdmin();
        $entityManager->flush();
        $reload = false;
        $currentUser = $this->getUser();
        $message = $usuario->isAdmin() ? 'Usuário agora é admin.' : 'Privilégios de admin removidos.';
        $category = $usuario->isAdmin() ? 'success' : 'error';
        if ($currentUser && $currentUser->getId() === $usuario->getId()) {
            $this->atualizarSessaoUsuario($usuario, $tokenStorage);
            $reload = false;
        }
        return $this->json([
            'success' => true,
            'admin' => $usuario->isAdmin(),
            'message' => $message,
            'category' => $category,
            'reload' => $reload
        ]);
    }

    #[Route('/admin/toggle-empresa/{id}', name: 'borae_toggle_empresa', methods: ['POST'])]
    public function toggleEmpresa(int $id, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
        }
        $usuario->ToggleEmpresa();
        $entityManager->flush();
        $reload = false;
        $currentUser = $this->getUser();
        $message = $usuario->isEmpresa() ? 'Usuário agora é empresa.' : 'Privilégios de empresa removidos.';
        $category = $usuario->isEmpresa() ? 'success' : 'error';
        if ($currentUser && $currentUser->getId() === $usuario->getId()) {
            $this->atualizarSessaoUsuario($usuario, $tokenStorage);
            $reload = false;
        }
        return $this->json([
            'success' => true,
            'empresa' => $usuario->isEmpresa(),
            'message' => $message,
            'category' => $category,
            'reload' => $reload
        ]);
    }

    #[Route('/admin/deletar-usuario/{id}', name: 'admin_deletar_usuario', methods: ['POST'])]
    public function deletarUsuario(int $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            $this->addFlash('error', 'Usuário não encontrado.');
        } else {
            $entityManager->remove($usuario);
            $entityManager->flush();
            $this->addFlash('success', 'Usuário deletado com sucesso.');
        }
        return $this->redirectToRoute('borae_admin');
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
