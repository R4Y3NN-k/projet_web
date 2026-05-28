<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', ChoiceType::class, [
                'choices' => [
                    'Unprofessional behavior' => 'unprofessional',
                    'Scam or fraud' => 'scam',
                    'Rude or disrespectful' => 'rude',
                    'Did not complete work' => 'incomplete_work',
                    'Low quality work' => 'low_quality',
                    'Non-payment' => 'non_payment',
                    'Other' => 'other',
                ],
                'label' => 'Reason for Report',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (provide details)',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Please provide detailed information about the issue...',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Report',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
