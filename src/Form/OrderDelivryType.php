<?php

namespace App\Form;

use App\Entity\Delivry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderDelivryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $this->getDelivryTypeChoices(),
                'label' => false
            ])
            ->add('time_slot', ChoiceType::class, [
                'choices' => $this->getDelivryTimeSlotChoices(),
                'label' => false
            ])
            ->add('day_slot', ChoiceType::class, [
                'choices' => $this->getDelivryDaySlotChoices(),
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delivry::class,
        ]);
    }


    private function getDelivryTypeChoices()
    {
        $choices = Delivry::TYPE;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    private function getDelivryTimeSlotChoices()
    {
        $choices = Delivry::TIME_SLOT;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    private function getDelivryDaySlotChoices()
    {
        $choices = Delivry::DAY_SLOT;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    
}
