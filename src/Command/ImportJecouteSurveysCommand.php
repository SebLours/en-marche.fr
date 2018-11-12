<?php

namespace AppBundle\Command;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\Jecoute\Choice;
use AppBundle\Entity\Jecoute\Question;
use AppBundle\Entity\Jecoute\Survey;
use AppBundle\Entity\Jecoute\SurveyQuestion;
use AppBundle\Jecoute\SurveyQuestionTypeEnum;
use AppBundle\Repository\AdherentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportJecouteSurveysCommand extends Command
{
    protected static $defaultName = 'app:jecoute-surveys:import';

    private const QUESTIONS = [
        [
            'content' => 'Quelle est la raison de votre présence à ... ?',
            'type' => SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE,
            'choices' => [
                'Résident',
                'Travail',
                'Tourisme',
                'Autre',
            ],
        ],
        [
            'content' => 'Sur une échelle de 1 à 5 comment évaluez-vous l’intérêt que vous porterez aux élections municipales de 2020 à ... (1 étant « aucun intérêt », 5 étant « grand intérêt »).',
            'type' => SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE,
            'choices' => [
                '1 - Aucun intérêt',
                '2 - Faible intérêt',
                '3 - Intérêt moyen',
                '4 - Intéressé(e)',
                '5 - Grant intérêt',
            ],
        ],
        [
            'content' => 'Etes-vous inscrit sur les listes électorales ?',
            'type' => SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE,
            'choices' => [
                'Oui et je connais mon bureau de vote',
                'Oui, mais je ne connais pas mon bureau de vote',
                'Non, mais je pense m\'inscrire pour les élections municipales de 2020',
                'Non et je ne pense pas m\'inscrire pour les élections municipales de 2020',
            ],
        ],
        [
            'content' => 'Quelle image avez-vous de ... ?',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],

        [
            'content' => 'Pour vous quelles sont les 3 choses à améliorer en priorité dans cette localité ? (précisez pour chaque point)',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],
        [
            'content' => 'A l’inverse, pour vous quelles sont les 3 choses qui fonctionnent le mieux ? (précisez pour chaque point)',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],
        [
            'content' => 'Et si vous étiez maire de ..., quelle serait votre première mesure ?',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],
        [
            'content' => 'Souhaiteriez-vous être directement associé à la prise de décisions dans votre ville ?',
            'type' => SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE,
            'choices' => [
                'Oui',
                'Non',
            ],
        ],
        [
            'content' => 'Si oui, de quelle façon aimeriez-vous y être associé ?',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],
        [
            'content' => 'Si non, pour quelle raison ne souhaitez-vous pas y être associé ?',
            'type' => SurveyQuestionTypeEnum::SIMPLE_FIELD,
        ],
        [
            'content' => 'Sur une échelle de 1 à 5 à combien estimez-vous l’association des citoyens de ... aux décisions prises au niveau municipal (1 étant « pas du tout » et 5 étant « totalement ») ?',
            'type' => SurveyQuestionTypeEnum::UNIQUE_CHOICE_TYPE,
            'choices' => [
                '1 - Pas du tout associé',
                '2 - Rarement associé',
                '3 - Régulièrement associé',
                '4 - Souvent associé',
                '5 - Totalement associé',
            ],
        ],
    ];

    private $em;
    private $adherentsRepository;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(EntityManagerInterface $em, AdherentRepository $adherentsRepository)
    {
        $this->em = $em;
        $this->adherentsRepository = $adherentsRepository;

        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->text('Start importing surveys');

        foreach ($this->adherentsRepository->findReferents() as $referent) {
            $this->importSurveyFor($referent);
        }

        $this->em->flush();

        $this->io->success('Done');
    }

    private function importSurveyFor(Adherent $referent): void
    {
        $survey = new Survey($referent, 'Formulaire prêt à l\'emploi', false);

        foreach (self::QUESTIONS as $questionDatas) {
            $question = new Question($questionDatas['content'], $questionDatas['type']);

            if (isset($questionDatas['choices'])) {
                foreach ($questionDatas['choices'] as $choice) {
                    $question->addChoice(new Choice($choice));
                }
            }

            $survey->addQuestion(new SurveyQuestion($survey, $question));
        }

        $this->em->persist($survey);
    }
}