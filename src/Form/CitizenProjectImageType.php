<?php

namespace AppBundle\Form;

use AppBundle\MediaGenerator\Command\CitizenProjectImageCommand;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitizenProjectImageType extends CitizenProjectMediaType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('emoji', TextType::class)
            ->add('city')
            ->add('departmentCode')
            ->add('preview', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CitizenProjectImageCommand::class,
        ]);
    }
}
