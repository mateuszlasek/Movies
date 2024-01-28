<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Review Content',
                'required' => false, // Ustaw na true, jeśli chcesz, aby to pole było wymagane
            ])
            ->add('rating', IntegerType::class, [
                'label' => 'Rating',
                'attr' => ['min' => 1, 'max' => 5], // Ustawienia dla pola IntegerType
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit Review',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
