<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Typeproduit;
use Symfony\Component\Form\AbstractType;
use App\Entity\TypeTypeproduitConditionnement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TypeproduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labeletype', TextType::class, [
                'required' => false,
                'label' => 'Label'
            ])
            ->add('caracteristiques', TextareaType::class, [
                'required' => false,
                'label' => 'Caractéristiques produit'
            ])
            ->add('delais', TextareaType::class, [
                'required' => false,
                'label' => 'Les plus produit'
            ])
            ->add('tarifs', TextareaType::class, [
                'required' => false,
                'label' => 'Tarif & Frais de port'
            ])
            ->add('reversement', TextType::class, [
                'required' => false,
                'label' => 'Reversement'
            ])
            ->add('attachements', FileType::class, [
                'label' => 'Photos produit',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => ['accept' => 'image/*']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Typeproduit::class,
        ]);
    }
}
