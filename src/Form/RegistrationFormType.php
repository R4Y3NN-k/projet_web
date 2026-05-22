<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\Location;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('fullAddress', TextType::class)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // I will handel the hashing myself in the contoller (Rayen)
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'placeholder' => 'Select your city...',
            ])
            ->add('serviceCategory', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Select your trade...',
                'mapped' => false,
                'required' => false,
            ])
            ->add('yearsOfExperience', IntegerType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('userType', TextType::class, [
                'mapped' => false,
                'data' => 'client', 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

?>