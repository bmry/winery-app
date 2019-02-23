<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/22/2019
 * Time: 11:54 AM
 */

namespace App\Form\Type;


use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('wine', EntityType::class, array(
        'required' => true,
        'placeholder' => 'Choose a wine',
        'class' => 'App\Entity\Wine',
        'choice_label' => 'title',
        'label' => false,
        'multiple' => false,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('w');
        },'attr' => [
                'class'=> "form-control"
            ]
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }

}