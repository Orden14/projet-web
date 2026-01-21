<?php

namespace App\Form;

use App\Entity\Folder;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\FolderRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RessourceType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly TagRepository $tagRepository,
        private readonly FolderRepository $folderRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'Dossier',
                'choices' => [
                    'Aucun' => null,
                    ...$this->folderRepository->findByUser($currentUser),
                ],
                'required' => true,
                'constraints' => [
                    new Callback(static function (?Folder $parent, ExecutionContextInterface $context) use ($currentUser): void {
                        if ($parent !== null && $parent->getOwner() !== $currentUser) {
                            $context
                                ->buildViolation('Le dossier sélectionné est invalide.')
                                ->addViolation()
                            ;
                        }
                    }),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => $this->categoryRepository->findByUser($currentUser),
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => $this->tagRepository->findByUser($currentUser),
                'multiple' => true,
                'required' => false,
            ])
            ->add('favorite', CheckboxType::class, [
                'label' => 'Favori',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
        ]);
    }
}
