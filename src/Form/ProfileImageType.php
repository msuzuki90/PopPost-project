<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //FileType will render upload input
            ->add('profileImage', FileType::class, [
                'label' => 'Profile Image (JPG or PNG file)',
                'mapped'=> false,
                'required'=> false,
                //prepared constraints
                'constraints'=> [
                    //added limited size import the constraints file class
                    //mimeTypes property for accepted file type shuch as jpeg or png (all mimeType are predifined)
                    new File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/jpeg',
                            'image/png',
                        ],
                        //customize error message
                        'mimeTypesMessage'=> 'Please upload a valid PNG or JPEG file'
                    ])
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
