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

    public function build(RessourceInterface $ressource): FormInterface
    {
        $ressourceType = $ressource->getType();

        return $this->formFactory->create($ressourceType->getCorrespondingFormTypeClass(), $ressource, [
            'action' => $this->router->generate('ressource_create', ['type' => $ressourceType->value]),
            'method' => 'POST',
        ]);
    }
}
