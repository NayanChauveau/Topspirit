<?php

namespace App\Form;

use App\Entity\Platform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PlatformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('content', null, [
                'help' => 'La description de la plateforme doit faire entre 20 et 300 caractères.'
            ])
            ->add('url')
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'help' => 'format de l\'image 200px x 250px'
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Platform::class,
        ]);
    }
}

