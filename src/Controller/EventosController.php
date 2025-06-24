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
    #[Route(name: 'app_eventos_index', methods: ['GET'])]
    public function index(EventoRepository $eventoRepository): Response
    {
        return $this->render('eventos/index.html.twig', [
            'eventos' => $eventoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_eventos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evento = new Evento();
        $form = $this->createForm(EventoForm::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evento);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eventos/new.html.twig', [
            'evento' => $evento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eventos_show', methods: ['GET'])]
    public function show(Evento $evento): Response
    {
        return $this->render('eventos/show.html.twig', [
            'evento' => $evento,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_eventos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evento $evento, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventoForm::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eventos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eventos/edit.html.twig', [
            'evento' => $evento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eventos_delete', methods: ['POST'])]
    public function delete(Request $request, Evento $evento, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evento->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evento);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eventos_index', [], Response::HTTP_SEE_OTHER);
    }
}
