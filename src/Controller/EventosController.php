<?php

namespace App\Controller;

use App\Entity\Evento;
use App\Form\EventoForm;
use App\Repository\EventoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/eventos')]
final class EventosController extends AbstractController
{
    #[Route(name: 'app_eventos_listar', methods: ['GET'])]
    public function index(EventoRepository $eventoRepository): Response
    {
        return $this->render('eventos/index.html.twig', [
            'eventos' => $eventoRepository->findAll(),
        ]);
    }

    #[Route('/gerenciar', name: 'app_eventos_gerenciar', methods: ['GET'])]
    public function manage(EventoRepository $eventoRepository): Response
    {
        return $this->render('eventos/index.html.twig', [
            'eventos' => $eventoRepository->findByCriador($this->getUser()),
        ]);
    }

    #[Route('/criar', name: 'app_eventos_criar', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = $this->getUser();
        if (!$usuario) {
            throw $this->createAccessDeniedException('Você precisa estar logado para criar um evento.');
        }

        $evento = new Evento();
        $form = $this->createForm(EventoForm::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evento->setCriador($this->getUser());
            $entityManager->persist($evento);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventos_listar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eventos/new.html.twig', [
            'evento' => $evento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eventos_mostrar', methods: ['GET'])]
    public function show(Evento $evento): Response
    {
        return $this->render('eventos/show.html.twig', [
            'evento' => $evento,
        ]);
    }

    #[Route('/editar/{id}', name: 'app_eventos_editar', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evento $evento, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $evento->getCriador()) {
            throw $this->createAccessDeniedException('Você não tem permissão para editar este evento.');
        }

        $form = $this->createForm(EventoForm::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eventos_listar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eventos/edit.html.twig', [
            'evento' => $evento,
            'form' => $form,
        ]);
    }

    #[Route('/deletar/{id}', name: 'app_eventos_deletar', methods: ['POST'])]
    public function delete(Request $request, Evento $evento, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $evento->getCriador()) {
            throw $this->createAccessDeniedException('Você não tem permissão para deletar este evento.');
        }

        if ($this->isCsrfTokenValid('delete' . $evento->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evento);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eventos_listar', [], Response::HTTP_SEE_OTHER);
    }
}