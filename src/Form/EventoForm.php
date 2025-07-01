<?php

namespace App\Form;

use App\Entity\Evento;
use App\Entity\Usuario;
use App\Enums\CategoriaEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome')
            ->add('data_inicio', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => true,
                'years' => [date('Y')],
            ])
            ->add('hora_inicio', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'with_seconds' => false,
])
            ->add('endereco')
             ->add('categoria', EnumType::class, [
                'class' => CategoriaEnum::class,
                'choice_label' => function (CategoriaEnum $choice) {
                    return $choice->getCategoria();
                },
                'placeholder' => 'Selecione uma categoria',
                'multiple' => false,
                'expanded' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evento::class,
        ]);
    }
}
