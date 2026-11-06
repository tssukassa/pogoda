<?php

namespace App\Form;

use App\Entity\Forecast;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForecastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data',
            ])
            ->add('temperature', NumberType::class, [
                'scale' => 1, // np. precyzja do 1 miejsca po przecinku (np. 21.5)
                'html5' => true,
                'label' => 'Temperatura (°C)',
            ])
            ->add('wind', NumberType::class, [
                'scale' => 1,
                'html5' => true,
                'label' => 'Wiatr (km/h)',
            ])
            ->add('humidity', NumberType::class, [
                'html5' => true,
                'label' => 'Wilgotność (%)',
            ])
            ->add('precipitation', NumberType::class, [
                'scale' => 2,
                'html5' => true,
                'label' => 'Opady (mm)',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => function (Location $location) {
                    // Wyświetlanie miasta i kraju w liście wyboru zamiast samego ID
                    return $location->getName() . ' (' . $location->getCountry() . ')';
                },
                'label' => 'Lokalizacja',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Forecast::class,
        ]);
    }
}
