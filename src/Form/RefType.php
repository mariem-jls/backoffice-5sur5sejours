<?php

namespace App\Form;

use App\Entity\Ref;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Typeref;

class RefType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libiller', TextType::class, [
                'required' => false,
                'label' => 'Libeller'
            ])
            ->add('code', TextType::class, [
                'required' => false,
                'label' => 'Code'
            ])
            ->add('typeref', EntityType::class, [
                'class' => Typeref::class,
                'choice_label' => 'name', // Adjust this based on your Typeref entity
                'label' => 'Type Reference'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ref::class,
        ]);
    }
}
