<?php
/**
 * Created by PhpStorm.
 * User: MerijnCampsteyn
 * Date: 29-Dec-15
 * Time: 11:45 PM.
 */

namespace App\Form\Type;

use App\Entity\Wine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Name',
                'required'=> true,
                'attr' => [
                    'autofocus' => true,
                    'class'=> "form-control"
                ],
            ))
            ->add('link', TextType::class, array(
                'label' => 'Link',
                'attr' => [
                    'required' => true,
                    'class'=> "form-control"
                ],
            ))
            ->add('guid', TextType::class, array(
                'label' => 'GUID',
                'required' => false,
                'attr' => [
                    'class'=> "form-control"
                ],
            ))
            ->add('publishDate',DateType::Class, array(
                'years' => range(date('Y'), date('Y')-100),
                'months' => range(date('m'), 12),
                'days' => range(date('d'), 31),
                'attr' => ['class' => "form-control"],
                'required' =>true
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class'=> "form-control"
                ],
            ))
            ->add('author', TextType::class, array(
                'label' => 'Author',
                'required' => false,
                'attr' => [
                    'class'=> "form-control"
                ],
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Wine::class,
            'cascade_validation' => true,
        ));
    }
    public function getName()
    {
        return 'app_wine';
    }
}
