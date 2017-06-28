<?php

namespace LouvreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('date', DateType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'js-datepicker'],
            'html5' => 'false',
            'format' => 'dd/MM/yyyy'
            ])
        ->add('ticketType', ChoiceType::class, array(
                'choices' => array(
                    'Journée' => 'Journée',
                    'Demi-journée' => 'Demi-journée'
                ),
                'expanded' => true
            ))
        ->add('nbrPersonnes', IntegerType::class, array(
            'attr' => array(
                'min' => 1
                )
            ))
        ->add('email', EmailType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LouvreBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'louvrebundle_commande';
    }


}
