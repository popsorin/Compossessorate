<?php

namespace App\FormType;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullname', TextType::class, [
            'label' => 'Nume',
            'attr' => ['class' =>'form-control']
        ])
            ->add('locality', TextType::class, [
                'label' => 'Localitate',
                'attr' => ['class' =>'form-control']
            ])
            ->add('street', TextType::class, [
                'label' => 'Strada',
                'attr' => ['class' =>'form-control']
            ])
            ->add('streetNr', TextType::class, [
                'label' => 'Numar casa',
                'attr' => ['class' =>'form-control']
            ])
            ->add('hectare', TextType::class, [
                'label' => 'Hectare',
                'attr' => ['class' =>'form-control']
            ])
            ->add('CISeries', TextType::class, [
                'label' => 'Serie',
                'attr' => ['class' =>'form-control']
            ])
            ->add('CINr', TextType::class, [
                'label' => 'Numar',
                'attr' => ['class' =>'form-control']
            ])
            ->add('CNP', TextType::class, [
                'label' => 'CNP',
                'attr' => ['class' =>'form-control']
            ])
            ->add('cubeMeters', TextType::class, [
                'label' => 'Metri cubi',
                'attr' => ['class' =>'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Salveaza',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}