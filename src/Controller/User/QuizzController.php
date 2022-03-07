<?php

namespace App\Controller\User;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Reward;
use App\Entity\User;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizzController extends AbstractController
{
    /**
     * @Route("/quizz", name="quizz")
     */
    public function index(): Response
    {
        $questions = $this->getDoctrine()->getRepository(Question::class)->findByDateIsPassed();

        return $this->render('quizz/index.html.twig', [
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/quizz/{id}", name="question")
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function questionById($id): Response
    {
        $question = $this->getDoctrine()->getRepository(Question::class)->findOneBy(['id' => $id]);
        $answers = $this->getDoctrine()->getRepository(Answer::class)->findBy(['question' => $question]);

        if ($question->getAvailableAt() < new \DateTime('now'))
        {
            return $this->render('quizz/question.html.twig', [
                'question' => $question,
                'answers' => $answers
            ]);
        }
        return $this->render('quizz/index.html.twig');
    }

    /**
     * @Route("/quizz/reward/{id}/{idAnswer}", name="reward")
     * @param $id
     * @param $idAnswer
     * @return Response
     */
    public function rewardById($id, $idAnswer): Response
    {
        if ($this->getDoctrine()->getRepository(Answer::class)->findOneBy(['id' => $idAnswer, 'question' => $id])->getIsCorrect())
        {
            $this->getDoctrine()->getRepository(Question::class)->findOneBy(['id' => $id])->setIsResolved(1);
            $this->getDoctrine()->getManager()->flush();

            $reward = $this->getDoctrine()->getRepository(Reward::class)->findOneBy(['question' => $id]);

            $this->addFlash('success', 'Bravo, c\'est la bonne réponse !');

            return $this->render('quizz/reward.html.twig', [
                'reward' => $reward
            ]);
        }
        $this->addFlash('danger', 'Tu t\'es trompée, recommence !');

        return $this->redirectToRoute('question', [
            'id' => $id,
        ]);
    }
}
