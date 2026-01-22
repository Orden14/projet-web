<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContactType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $builder
            ->add('contactName', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('contactNumber', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('contactEmail', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choices' => $this->categoryRepository->findByUser($currentUser),
                'choice_label' => 'name',
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
