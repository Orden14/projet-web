<?php

namespace App\Factory;

use App\Interface\RessourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class RessourceFormFactory
{
    public function __construct(
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function build(RessourceInterface $ressource, bool $isEditForm = false): FormInterface
    {
        $ressourceType = $ressource->getType();

        $routeParameters = $isEditForm
            ? ['id' => $ressource->getId()]
            : ['type' => $ressourceType->value];

        return $this->formFactory->create($ressourceType->getCorrespondingFormTypeClass(), $ressource, [
            'action' => $this->router->generate($isEditForm ? 'ressource_edit' : 'ressource_create', $routeParameters),
            'method' => 'POST',
        ]);
    }
}
