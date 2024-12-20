<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setUpdatedAt(new \DateTimeImmutable());
        $comment->setUserId($this->getUser()->getId());

        if ($request->query->has('eventId')) {
            $event = $entityManager->getRepository(Event::class)->find($request->query->get('eventId'));
            if ($event) {
                $comment->setEvent($event);
                $comment->setEventId($event->getId());
            }
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($comment->getEvent()) {
                $comment->setEventId($comment->getEvent()->getId());
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été publié.');

            return $this->redirectToRoute('app_event_show', [
                'id' => $comment->getEvent()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($comment->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce commentaire.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été modifié.');

            return $this->redirectToRoute('app_event_show', [
                'id' => $comment->getEvent()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'event' => $comment->getEvent()
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($comment->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce commentaire.');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé.');
        }

        return $this->redirectToRoute('app_event_show', [
            'id' => $comment->getEvent()->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}