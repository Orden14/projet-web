<?php

namespace App\Form;

use App\Entity\Url;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'URL',
            ])
            ->add('ressource', RessourceType::class, [
                'data' => $builder->getData(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Url::class,
        ]);
    }
}
