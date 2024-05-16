<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render('conference/index.html.twig', [
                        'conferences' => $conferenceRepository->findAll(),
                    ]);
    }

    #[Route('/conference/{Slug}', name: 'conference')]
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository): Response
        {
            $comment = new Comment();
           $form = $this->createForm(CommentFormType::class, $comment);
            $offset = max(0, $request->query->getInt('offset', 0));
            $paginator = $commentRepository->getCommentPaginator($conference, $offset);
            return ($this->render('conference/show.html.twig', [
                'conferences' => $conferenceRepository->findAll(),
                'conference' => $conference,
                'comments' => $paginator,
                            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
                            'comment_form' => $form,
            ]));
        }
}
