<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/questions", name="api_get_questions", methods={"GET"})
     */
    public function getQuestions()
    {
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();

        $data = [];
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'title' => $question->getTitle(),
                'description' => $question->getDescription(),
                'score' => $question->getScore(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/question/{id}", name="api_get_question", methods={"GET"})
     */
    public function getQuestion(Question $question)
    {
        $data = [
            'id' => $question->getId(),
            'title' => $question->getTitle(),
            'description' => $question->getDescription(),
            'score' => $question->getScore(),
        ];

        return new JsonResponse($data);
    }

/**
 * @Route("/api/question", name="api_create_question", methods={"POST"})
 * @IsGranted("ROLE_USER")  // L'annotation pour vérifier les autorisations
 */
    public function createQuestion(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $question = new Question();
        $question->setTitle($data['title']);
        $question->setDescription($data['description']);
        $question->setScore(0); // Automatically initialized

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($question);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Question created successfully'], 201);
    }

/**
 * @Route("/api/up/{id}", name="api_increment_score", methods={"PATCH"})
 * @IsGranted("ROLE_ADMIN")  // L'annotation pour vérifier les autorisations
 */
    public function incrementScore(Question $question)
    {
        $question->setScore($question->getScore() + 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Score incremented successfully']);
    }

/**
 * @Route("/api/down/{id}", name="api_decrement_score", methods={"PATCH"})
 * @IsGranted("ROLE_ADMIN")  // L'annotation pour vérifier les autorisations
 */ 
    public function decrementScore(Question $question)
    {
        $question->setScore($question->getScore() - 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Score decremented successfully']);
    }
}
