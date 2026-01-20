<?php

namespace App\Factory;

use App\Entity\Contact;
use App\Simple\UserOwnedEntityData;

final class ContactFactory extends AbstractFactory
{
    public function build(UserOwnedEntityData $userOwnedEntityData, string $contactName, string $contactNumber, string $contactEmail): void
    {
        $contact = new Contact();

        $contact
            ->setOwner($userOwnedEntityData->getOwner())
            ->setTitle($userOwnedEntityData->getTitle())
            ->setDescription($userOwnedEntityData->getDescription())
            ->setCategory($userOwnedEntityData->getCategory())
            ->setContactName($contactName)
            ->setContactNumber($contactNumber)
            ->setContactEmail($contactEmail)
        ;

        $this->entity = $contact;
    }

    /**
     * @return Contact
     */
    public function grabEntity(): object
    {
        return $this->entity ?? new Contact();
    }
}
