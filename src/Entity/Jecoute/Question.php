<?php

namespace AppBundle\Entity\Jecoute;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use AppBundle\Jecoute\SurveyQuestionTypeEnum;
use AppBundle\Validator\SurveyQuestionTypeChoice;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="jecoute_question")
 * @ORM\Entity
 *
 * @SurveyQuestionTypeChoice
 *
 * @Algolia\Index(autoIndex=false)
 */
class Question
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var SurveyQuestion[]|Collection
     *
     * @ORM\OneToMany(targetEntity="SurveyQuestion", mappedBy="question", cascade={"persist", "remove"})
     *
     * @Assert\Valid
     */
    private $surveys;

    /**
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @JMS\Groups({"survey_list"})
     */
    private $content;

    /**
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Choice(callback={"AppBundle\Jecoute\SurveyQuestionTypeEnum", "all"})*
     *
     * @JMS\Groups({"survey_list"})
     */
    private $type;

    /**
     * @var Choice[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="question", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position": "ASC"})
     *
     * @Assert\Valid
     *
     * @JMS\Groups({"survey_list"})
     */
    private $choices;

    public function __construct(string $content = null, string $type = null)
    {
        $this->content = $content;
        $this->type = $type;
        $this->surveys = new ArrayCollection();
        $this->choices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurveyQuestion(SurveyQuestion $surveyQuestion): void
    {
        if (!$this->surveys->contains($surveyQuestion)) {
            $surveyQuestion->setQuestion($this);
            $this->surveys->add($surveyQuestion);
        }
    }

    public function removeSurveyQuestion(Survey $survey): void
    {
        $this->surveys->removeElement($survey);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function addChoice(Choice $choice): void
    {
        if (!$this->choices->contains($choice)) {
            $choice->setQuestion($this);
            $this->choices->add($choice);
        }
    }

    public function removeChoice(Choice $choice): void
    {
        $this->choices->removeElement($choice);
    }

    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function setChoices(array $choices): void
    {
        $this->choices = $choices;
    }

    public function isChoiceType(): bool
    {
        return SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE == $this->type
            || SurveyQuestionTypeEnum::MULTIPLE_CHOICE_TYPE == $this->type
        ;
    }
}
