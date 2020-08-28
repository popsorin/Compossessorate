<?php


namespace App\FormType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class PaginateDocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numberOfDocuments', ChoiceType::class, [
            'label' => " ",
            'choices' => [
                '10' => 10,
                '20' => 20,
                '30' => 30,
                '40' => 40,
                '50' => 50,
            ],
        ]);
        $builder->add('submit', SubmitType::class, [
            "attr" => ["class" => "btn btn-dark nav-link btn-lg"]
        ]);
    }
}