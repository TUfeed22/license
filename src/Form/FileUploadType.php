<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Выбрать файл',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-3',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Получить лицензии',
                'attr' => [
                    'class' => 'btn btn-primary mt-3',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => null,
        ]);
    }
}