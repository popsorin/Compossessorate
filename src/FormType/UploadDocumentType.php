<?php

namespace App\FormType;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('compossessorate_table', FileType::class, [
            "label" => " ",
            "attr" => ['class' => "btn btn-dark mt-0 float-left nav-link"],
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'text/csv',
                        'text/plain',
                        'application/pdf',
                        'application/x-pdf',
                    ],
                    'mimeTypesMessage' => 'Please upload a CSV document',
                ])
                ]
            ]);
        $builder->add('submit', SubmitType::class, [
            "attr" => ["class" => "btn btn-dark nav-link"]
            ]);
        $builder->setAction("document/upload");
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Document::class]);
    }
}