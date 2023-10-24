<?php

namespace App\Form;

use App\Entity\Component;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UpdateComponentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('code', TextareaType::class)
            ->add('price', NumberType::class)
            ->add('type')
            ->add('DesignSystem', ChoiceType::class, [
                'choices' => $options['userDesignSystems'],
                'choice_label' => 'name', // Remplacez 'name' par le nom du champ du design system
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Component::class,
        ])
            ->setRequired('userDesignSystems');;
    }
}
