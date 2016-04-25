<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Response
 *
 * @ORM\Table(name="response")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponseRepository")
 */
class Response
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ManyToOne(targetEntity="User", inversedBy="response")
     * @JoinColumn(name="User", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Survey
     *
     * @ManyToOne(targetEntity="Survey", inversedBy="response")
     * @JoinColumn(name="Survey", referencedColumnName="id")
     */
    private $survey;

    /**
     * @var Question
     *
     * @ManyToOne(targetEntity="Question", inversedBy="response")
     * @JoinColumn(name="Question", referencedColumnName="id")
     */
    private $question;

    /**
     * @var Answer
     *
     * @ManyToOne(targetEntity="Answer", inversedBy="response")
     * @JoinColumn(name="Answer", referencedColumnName="id")
     */
    private $answer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolean", type="boolean", nullable=true)
     */
    private $boolean;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;


// constructor


// setter, getter

    /**
     * @return boolean
     */
    public function isBoolean()
    {
        return $this->boolean;
    }

    /**
     * @param boolean $boolean
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param Answer $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

}
