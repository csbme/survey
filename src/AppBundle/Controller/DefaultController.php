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
