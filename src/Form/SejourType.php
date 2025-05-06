<?php

namespace App\Form;

use App\Entity\Sejour;
use App\Entity\Ref;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SejourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeSejour', TextType::class, [
                'required' => false,
                'label' => 'Code Sejour',
            ])
            ->add('dateCreationCode', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date Creation Code',
            ])
            ->add('dateFinCode', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date Fin Code',
            ])
            ->add('themSejour', TextType::class, [
                'required' => false,
                'label' => 'Them Sejour',
            ])
            ->add('adresseSejour', TextType::class, [
                'required' => false,
                'label' => 'Adresse Sejour',
            ])
            ->add('pays', TextType::class, [
                'required' => false,
                'label' => 'Pays',
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'label' => 'Ville',
            ])
            ->add('codePostal', IntegerType::class, [
                'required' => false,
                'label' => 'Code Postal',
            ])
            ->add('dateDebutSejour', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date Debut Sejour',
            ])
            ->add('dateFinSejour', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date Fin Sejour',
            ])
            ->add('nbPhotoDiposer', IntegerType::class, [
                'required' => false,
                'label' => 'Nb Photo Diposer',
            ])
            ->add('paym', IntegerType::class, [
                'required' => false,
                'label' => 'Paym',
            ])
            ->add('albumgratuie', IntegerType::class, [
                'required' => false,
                'label' => 'Album Gratuie',
            ])
            ->add('nbMessage', IntegerType::class, [
                'required' => false,
                'label' => 'Nb Message',
            ])
            ->add('etatAcompAlbum', CheckboxType::class, [
                'required' => false,
                'label' => 'Etat Acomp Album',
            ])
            ->add('etatAdresseCarte', CheckboxType::class, [
                'required' => false,
                'label' => 'Etat Adresse Carte',
            ])
            ->add('statut', EntityType::class, [
                'class' => Ref::class,
                'choice_label' => 'name', // Adjust according to your entity field
                'required' => false,
                'label' => 'Statut',
            ]);
            // Add other fields as necessary
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sejour::class,
        ]);
    }
}
