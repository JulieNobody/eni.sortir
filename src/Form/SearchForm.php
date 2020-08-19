<?php


namespace App\Form;


use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class SearchForm extends AbstractType implements FormTypeInterface
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => 'Mot clé',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])

            ->add('campus', EntityType::class, [
                'label' => 'Site',
                'required' => true,
                'class' => Campus::class
            ])


            ->add('min', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Entre',
                'required' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('max', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Et',
                'required' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('isOrga', CheckboxType::class,[
                'label' => 'Je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class,[
                'label' => 'Je suis inscrit/e',
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class,[
                'label' => 'Je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('sortiesPassees', CheckboxType::class,[
                'label' => 'Sorties passées',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}