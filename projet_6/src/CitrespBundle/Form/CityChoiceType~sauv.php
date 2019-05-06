<?php

namespace CitrespBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CityChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('choiceCity', EntityType::class, [
              'class' => 'CitrespBundle:BaseCities',
              'choice_label' => 'getCityLabel',
              'required' => true,
              'label' => 'Sélectionnez votre ville',
              'query_builder' => function (EntityRepository $er){
                       return $er->createQueryBuilder('c')
                         ->orderBy('c.nomCommune', 'ASC');
                     }
          ]);
    }

}
