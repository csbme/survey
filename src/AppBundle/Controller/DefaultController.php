<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Entity\Answer,
    AppBundle\Entity\Question,
    AppBundle\Entity\Response as EntityResponse,
    AppBundle\Entity\Survey,
    AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }


    /**
     * @Route("survey", name="survey")
     */
    public function surveyAction(Request $request)
    {
        $surveyId = $request->get('surveyId');
        $questionId = $request->get('questionId');

        if (!$questionId) {

            $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a
JOIN a.question q
JOIN q.survey s
WHERE s.id = " . $surveyId . "
");
            $result = $query->getResult();

            $countQuestion = 0;
            foreach ($result as $value){

                $currentQuestionId = $value->getQuestion()->getId();
                if(!empty($previousQuestionId) and $previousQuestionId == $currentQuestionId)
                {
                    continue;
                }
                $countQuestion++;
                $previousQuestionId = $value->getQuestion()->getId();
            }
            $questionId = $result[0]->getQuestion()->getId();
        }
        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a
JOIN a.question q
JOIN q.survey s
WHERE s.id = " . $surveyId . " and q.id = " . $questionId . "

");
        $result = $query->getResult();



        return $this->render('show/view.html.twig', array(
            'questionCount' => $countQuestion,
            'data' => $result,
            'questionId' => $questionId ? $questionId : null,
        ));
    }


    /**
     * @Route("view_data")
     */
    public function viewData(Request $request)
    {
        $questionId = $request->get('questionId');
        $surveyId = $request->get('surveyId');
        $booleanArray = $request->get('boolean');

        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT s
FROM AppBundle\Entity\Survey s
WHERE s.id = " . $surveyId . "

");
        $survey = $query->getResult()[0];

        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT q
FROM AppBundle\Entity\Question q
WHERE q.id = " . $questionId . "

");
        $question = $query->getResult()[0];
        $em = $this->getDoctrine()->getManager();

        foreach ($booleanArray as $key => $value) {

            $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a
FROM AppBundle\Entity\Answer a
WHERE a.id = " . $key . "

");
            $answer = $query->getResult()[0];

            $response = new EntityResponse;
            $response->setSurvey($survey);
            $response->setQuestion($question);
            $response->setAnswer($answer);
            $response->setBoolean($value);
            $em->persist($response);
        }

        $em->flush();

            return $this->redirectToRoute('nextQuestion', array(
                'surveyId' => $surveyId,
                'questionId' => $questionId,
            ));
    }


    /**
     * @Route("nextQuestion")
     */
    public function nextQuestion(Request $request)
    {
        $surveyId = $request->get('$surveyId');
        $questionId = $request->get('questionId');
        $questionHistory = $request->get('questionHistory');


        $surveyId=51;
        $questionId=735;

        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a
JOIN a.question q
JOIN q.survey s
WHERE s.id = " . $surveyId . "
");

        /*
         * WHERE s.id = " . $surveyId . " and q.id > " . $questionId . "
         * */

        $result = $query->getResult();

        if(!$result)
        {
            return new Response('done');
        }


        $nextId = null;
        $previousId = null;

        foreach ($result as $value) {
            $currentId = $value->getQuestion()->getId();

            if (is_null($previousId)) {
                $previousId = $currentId;
                $nextId = $currentId;
                continue;
            }

            if ($currentId < $previousId and $currentId > $questionId) {
                $nextId = $currentId;
                continue;
            }

            $previousId = $value->getQuestion()->getId();
        }


        $questionId = $request->get('questionId');
        $surveyId = $request->get('surveyId');
        $booleanArray = $request->get('boolean');


        return $this->redirectToRoute('nextQuestion', array(
            'surveyId' => $surveyId,
            'questionId' => $questionId,
        ));

    }







    /**
     * @Route("/abc")
     */
    function abc()
    {

        $id = 735;

        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a, q, s
FROM AppBundle\Entity\Answer a

JOIN a.question q

JOIN q.survey s

WHERE a.question = " . $id . "

");

        $result = $query->getResult();

        dump($result);


        dump($result[0]->getQuestion()->getSurvey()->getTopic());


        return $this->render('show/show.html.twig', array('data' => $result));


    }


    /**
     * @Route("response_data")
     *
     * @param Request $request
     * @return Response
     */
    function checkResponse(Request $request)
    {
        $answer = $request->get('answer_id');
        $question = $request->get('question_id');
        $survey = $request->get('survey_id');


        $query = $this->getDoctrine()->getManager()->createQuery("
SELECT a.boolean
FROM AppBundle\Entity\Answer a
JOIN a.question q
JOIN q.survey s

WHERE s.id = " . $survey . " and q.id = " . $question . " and a.id = " . $answer . "

");

        $result = $query->getResult()[0]['boolean'];


        $answerDb = $this->getDoctrine()
            ->getRepository('AppBundle:Answer')
            ->find($answer);

        $questionDb = $this->getDoctrine()
            ->getRepository('AppBundle:Question')
            ->find($question);

        $surveyDb = $this->getDoctrine()
            ->getRepository('AppBundle:Survey')
            ->find($survey);

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find(1);


        $response = new EntityResponse();
        $response->setBoolean($result);

        $response->setAnswer($answerDb);
        $response->setQuestion($questionDb);
        $response->setSurvey($surveyDb);
        $response->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($response);
        $em->flush();

        return new Response($result ? 'Richtig' : 'Falsch');

    }


    private function ipAddress(Request $request)
    {
        return $request->getClientIp();
    }


    private function generateToken()
    {
        return base64_encode(random_bytes(24));
    }


    /**
     * @Route("user")
     */
    public function createUserAction(Request $request)
    {
        $user = new User();

        $user->setToken($this->generateToken());
        $user->setIp($this->ipAddress($request));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        echo 'user created successfully';

        return new Response();
    }

}
