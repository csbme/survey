<?php
/**
 * Created by PhpStorm.
 * User: npc
 * Date: 4/17/16
 * Time: 2:16 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Exception;

use AppBundle\Entity\Answer,
    AppBundle\Entity\Question,
    AppBundle\Entity\Response as EntityResponse,
    AppBundle\Entity\Survey,
    AppBundle\Entity\User;

class CreatorController extends Controller
{
    const INPUTMISSING = 'Unvollständige Eingabe';
    const SURVEYMISSING = 'Umfrage muss zuerst erstellt werden';
    const NOTFOUND = 'Umfrage existiert nicht (404)';


    /**
     * @Route("create/survey", name="survey")
     */
    public function createSurveyAction(Request $request)
    {
        $errorMsg = $request->get('errorMsg');

        $subject = $request->get('subject');
        $topic = $request->get('topic');
        $semester = $request->get('semester');


        if (!$subject and !$topic and !$semester) {
            return $this->render('create/survey.html.twig');
        }

        if (!$subject or !$topic) {
            return $this->render('create/survey.html.twig', array(
                'errorMsg' => self::INPUTMISSING,
                'subject' => $subject,
                'topic' => $topic,
                'semester' => $semester,
            ));

        }

        $survey = new Survey();
        $survey->setSubject($subject);
        $survey->setTopic($topic);
        $semester ? $survey->setSemester($semester) : null;

        $em = $this->getDoctrine()->getManager();

        $em->persist($survey);
        $em->flush();

        $surveyId = $survey->getId();


        return $this->redirectToRoute('question', array('surveyId' => $surveyId));
    }


    /**
     * @Route("create/question", name="question")
     */
    public function createQuestionAction(Request $request)
    {
//        $errorMsg = $request->get('errorMsg');

        $surveyId = $request->get('surveyId');
        $questionText = $request->get('question');
        $answerArray = ($request->get('answer'));
        $booleanArray = ($request->get('boolean'));

        $submitButton = $request->get('action');
        $loop = ($request->get('loop'));


        if ($loop) {
            $query = $this->getDoctrine()->getManager()->createQuery("
                SELECT q
                FROM AppBundle\Entity\Question q
                WHERE q.survey = " . $surveyId . "
            ");

            $totalQuestions = count($query->getResult());

        } else {
            $totalQuestions = 0;
        }


        if (!$surveyId) {
            return $this->render('create/survey.html.twig', array(
                'errorMsg' => self::SURVEYMISSING,
            ));
        }


        if (!$questionText and !$answerArray and !$booleanArray) {
            return $this->render('create/question.html.twig', array(
                'surveyId' => $surveyId,
                'totalQuestions' => $totalQuestions,
            ));
        }


        if (empty($questionText) or empty($answerArray[0])) {
            return $this->render('create/question.html.twig', array(
                'errorMsg' => self::INPUTMISSING,
                'surveyId' => $surveyId,
                'questionText' => $questionText,
                'answer' => $answerArray,
                'boolean' => $booleanArray,
                'totalQuestions' => $totalQuestions,
            ));
        }


        $survey = $this->getDoctrine()
            ->getRepository('AppBundle:Survey')
            ->find($surveyId);


        $question = new Question();
        $question->setText($questionText);
        $question->setSurvey($survey);


        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();


        $booleanCompleteArray = array_fill(0, count($answerArray), false);

        foreach ($booleanArray as $key => $value) {
            $booleanCompleteArray[$key] = boolval($value);
        }

        foreach ($answerArray as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $answer = new Answer();
            $answer->setText($value);
            $answer->setBoolean($booleanCompleteArray[$key]);
            $answer->setQuestion($question);
            $em->persist($answer);
        }

        $em->flush();

        if ($submitButton == 'next') {
            return $this->redirectToRoute('question', array(
                'request' => $request,
                'surveyId' => $surveyId,
                'loop' => true,
                'totalQuestions' => $totalQuestions,
            ));
        }

        if ($submitButton == 'finish') {
            return $this->redirectToRoute('overview', array(
                'surveyId' => $surveyId,
            ));
        }
        return null;
    }


    /**
     * @Route("create/overview", name="overview")
     */
    public function overviewAction(Request $request)
    {
        $surveyId = $request->get('surveyId');


        if (!$surveyId) {
            return $this->render('base.html.twig', array(
                'errorMsg' => self::NOTFOUND,
            ));
        }

        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a

JOIN a.question q

JOIN q.survey s

WHERE s.id = " . $surveyId . "

");

        $result = $query->getResult();

        return $this->render('/create/overview.html.twig', array(
            'data' => $result,
        ));
    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("create/edit", name="edit")
     */
    public function editAction(Request $request)
    {
        $surveyId = $request->get('surveyId');
        $questionId = $request->get('questionId');


        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a

JOIN a.question q

JOIN q.survey s

WHERE s.id = " . $surveyId . " and q.id = " . $questionId . "

");

        $result = $query->getResult();


        return $this->render('/create/edit.html.twig', array(
            'data' => $result,
            'surveyId' => $surveyId,
            'questionId' => $questionId,
        ));
    }


    /**
     * @Route("create/edit_data")
     */
    public function editDataAction(Request $request)
    {
        $submitButton = $request->get('action');

        $surveyId = $request->get('surveyId');
        $questionId = $request->get('questionId');

        $questionText = $request->get('question');
        $answerArray = ($request->get('answer'));


        $booleanArray = ($request->get('boolean'));
        $loop = ($request->get('loop'));


        if ($submitButton != 'save') {
            return $this->redirectToRoute('overview', array(
                'surveyId' => $surveyId,
            ));
        };


        $em = $this->getDoctrine()->getManager();

        $question = $this->getDoctrine()
            ->getRepository('AppBundle:Question')
            ->find($questionId);

        $question->setText($questionText);
        $em->flush();


        foreach ($answerArray as $key => $innerArray) {
            foreach ($innerArray as $id => $value) {

                if (empty($value)) {
                    continue;
                }

                $answer = $this->getDoctrine()
                    ->getRepository('AppBundle:Answer')
                    ->find($id);

                $answer->setText($value);

                if (!empty($booleanArray[$id]) and boolval($booleanArray[$id])) {
                    $answer->setBoolean(true);
                } else {
                    $answer->setBoolean(false);
                }

                $answer->setQuestion($question);
                $em->flush();
            }
        }

        return $this->editAction($request);
    }


    /**
     * @Route("view", name="view")
     */
    public function viewAll()
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT q, s
FROM AppBundle\Entity\Question q

JOIN q.survey s

");

        $result = $query->getResult();



        return $this->render('/create/view.html.twig', array(
            'data' => $result,
        ));



        return new Response('hi');
    }

}