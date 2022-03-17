<?php

namespace App\Form;

use App\Entity\CartSubscription;
use App\Service\Paypal\PaypalService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartSubscriptionType extends AbstractType
{
    protected $paypalService;

    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idProductPlanPaypal', ChoiceType::class, [
                'choices' =>  $this->getProducts(),
                'required' => false,
                'label' => 'Nom du produit repertorié dans PayPal'
            ])
            ->add('nameSubscriptionPlan', ChoiceType::class, [
                'label' => 'Nom abonnement',
                'choices' => [
                    'Grand panier' => 'Grand panier',
                    'Panier moyen' => 'Panier moyen',
                    'Petit panier' => 'Petit panier'
                ]
            ])
            ->add('descriptionSubscriptionPlan')
            ->add('interval_unit', ChoiceType::class, [
                'label' => 'Cycle de paiment',
                'choices' => [
                    'SEMAINE' => 'WEEK',
                    'MOIS' => 'MONTH'
                ]
            ])
            ->add('detailedDescription', CKEditorType::class)
            ->add('priceSubscription')
            ->add('durationMonthSubscription', null, [
                'label' => 'Durrée de l\'engagement. (En mois)',
                'attr' => [
                    'min' => 0,
                    'max' => 999
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CartSubscription::class,
        ]);
    }

    // Permet de recupérer les produits dans PayPal
    public function getProducts()
    {
        $choices = $this->paypalService->getAllProducts();
        $output = [];
        foreach ($choices as $value) {
            // On ignore le produit 'PROD-20S96309WL105335A', car il est créer accidentellement
            // Du coup, PayPal n'a pas encore de point d'API pour supprimer les produits
            if ($value['id'] == 'PROD-20S96309WL105335A') {
                
            } else {
                $output[$value['name']] = $value['id'];
            }
            
        }
        return $output;
    }
}
