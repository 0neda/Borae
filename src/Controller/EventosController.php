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
    #[Route(name: 'borae_eventos_lista', methods: ['GET', 'POST'])]
    public function index(Request $request, EventoRepository $eventoRepository, EntityManagerInterface $entityManager): Response
    {
        $usuario = $this->getUser();
        $form = null;
        $formEdicao = null;

        if ($usuario) {
            $evento = new Evento();
            $form = $this->createForm(EventoForm::class, $evento);
            $form->handleRequest($request);

            $eventoEdicao = new Evento();
            $formEdicao = $this->createForm(EventoForm::class, $eventoEdicao);

            if ($form->isSubmitted() && $form->isValid()) {
                $evento->setCriador($usuario);
                $entityManager->persist($evento);
                $entityManager->flush();

                $this->addFlash('success', 'Evento criado com sucesso!');
                return $this->redirectToRoute('borae_eventos_lista', [], Response::HTTP_SEE_OTHER);
            }
        }

        // Monta um array de forms de edição para cada evento do usuário logado
        $eventos = $eventoRepository->findAllOrderByDataInicio();
        $formsEdicao = [];
        foreach ($eventos as $ev) {
            if ($usuario && $ev->getCriador() === $usuario) {
                $formsEdicao[$ev->getId()] = $this->createForm(EventoForm::class, $ev)->createView();
            }
        }
        return $this->render('eventos/index.html.twig', [
            'eventos' => $eventos,
            'form' => $form?->createView(),
            'formsEdicao' => $formsEdicao,
        ]);
    }

    #[Route('/gerenciar', name: 'borae_eventos_gerenciar', methods: ['GET', 'POST'])]
    public function manage(Request $request, EventoRepository $eventoRepository, EntityManagerInterface $entityManager): Response
    {
        $usuario = $this->getUser();
        $form = null;
        $formsEdicao = [];

        if ($usuario) {
            $evento = new Evento();
            $form = $this->createForm(EventoForm::class, $evento);
            $form->handleRequest($request);
        }

        if ($form && $form->isSubmitted() && $form->isValid()) {
            if ($usuario) {
                $evento->setCriador($usuario);
                $entityManager->persist($evento);
                $entityManager->flush();
                $this->addFlash('success', 'Evento criado com sucesso!');
                return $this->redirectToRoute('borae_eventos_gerenciar', [], Response::HTTP_SEE_OTHER);
            }
        }

        $eventos = $usuario ? $eventoRepository->findByCriador($usuario) : [];
        foreach ($eventos as $ev) {
            $formsEdicao[$ev->getId()] = $this->createForm(EventoForm::class, $ev)->createView();
        }
        return $this->render('eventos/index.html.twig', [
            'eventos' => $eventos,
            'form' => $form?->createView(),
            'formsEdicao' => $formsEdicao,
        ]);
    }



    #[Route('/{id}', name: 'borae_eventos_detalhes', methods: ['GET'])]
    public function show(Evento $evento): Response
    {
        return $this->render('eventos/show.html.twig', [
            'evento' => $evento,
        ]);
    }

    #[Route('/editar/{id}', name: 'borae_eventos_editar', methods: [
        'GET',
        'POST',
    ])]
    public function edit(
        Request $request,
        Evento $evento,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getUser() !== $evento->getCriador()) {
            throw $this->createAccessDeniedException(
                'Você não tem permissão para editar este evento.',
            );
        }

        $form = $this->createForm(EventoForm::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'borae_eventos_lista',
                [],
                Response::HTTP_SEE_OTHER,
            );
        }

        return $this->render('eventos/edit.html.twig', [
            'evento' => $evento,
            'formEvento' => $form,
        ]);
    }

    #[Route('/deletar/{id}', name: 'borae_eventos_deletar', methods: ['POST'])]
    public function delete(
        Request $request,
        Evento $evento,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getUser() !== $evento->getCriador()) {
            throw $this->createAccessDeniedException(
                'Você não tem permissão para deletar este evento.',
            );
        }

        if ($this->isCsrfTokenValid(
            'delete' . $evento->getId(),
            $request->request->get('_token'),
        )) {
            $entityManager->remove($evento);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'borae_eventos_lista',
            [],
            Response::HTTP_SEE_OTHER,
        );
    }
}
