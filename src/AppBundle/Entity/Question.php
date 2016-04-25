<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * Class Question
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 */
class Question
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
     * @var text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var Survey
     *
     * @ManyToOne(targetEntity="Survey", inversedBy="question")
     * @JoinColumn(name="survey", referencedColumnName="id")
     */
    private $survey;

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

    /**
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="Answer", mappedBy="question")
     */
    private $answer;

    /**
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="Response", mappedBy="question")
     */
    private $response;


// constructor

    /**
     * Question constructor.
     */
    public function __construct() {
        $this->answer = new ArrayCollection();
        $this->response = new ArrayCollection();
    }


// setter, getter

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
     * @return text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param text $text
     */
    public function setText($text)
    {
        $this->text = $text;
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

}
