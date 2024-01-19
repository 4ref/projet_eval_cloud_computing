<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['attr' => ['required' => true]])
            ->add('nom', TextType::class, ['attr' => ['required' => true]])
            ->add('prenom', TextType::class, ['attr' => ['required' => true]])
            ->add('email', EmailType::class, ['attr' => ['required' => true]])
            ->add('civilite', ChoiceType::class,
                [
                    'attr' => ['required' => true],
                    'choices' => [
                        'Homme' => 'homme',
                        'Femme' => 'femme',
                    ],
                    'expanded' => false,
                    'multiple' => false,
                    'label' => 'Civilité',
                ])
            ->add('editer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
