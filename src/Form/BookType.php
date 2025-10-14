<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref', TextType::class, [
                'label' => 'Reference',
                'attr' => ['class' => 'form-control']
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Science-Fiction' => 'Science-Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('publicationDate', DateType::class, [
                'label' => 'Publication Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('author', EntityType::class, [
                'label' => 'Author',
                'class' => Author::class,
                'choice_label' => 'username',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}