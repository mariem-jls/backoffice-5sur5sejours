<?php

namespace App\Form;

use App\Entity\Promotions;
use App\Entity\Ref;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PromotionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, ['required' => false])
            ->add('dateCreation', DateTimeType::class, ['required' => false])
            ->add('dateDebut', DateTimeType::class, ['required' => false])
            ->add('dateFin', DateTimeType::class, ['required' => false])
            ->add('nbreMaxGeneral', IntegerType::class, ['required' => false])
            ->add('nbreMaxParUser', IntegerType::class, ['required' => false])
            ->add('type', TextType::class, ['required' => false])
            ->add('etat', CheckboxType::class, ['required' => false])
            ->add('statut', EntityType::class, [
                'class' => Ref::class,
                'choice_label' => 'name', // Adjust this based on your Ref entity
                'required' => false,
            ])
            ->add('pourcentage', IntegerType::class, ['required' => false])
            ->add('strategie', TextType::class, ['required' => false])
            ->add('nbreApplicable', IntegerType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotions::class,
        ]);
    }
}
