<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            EmailField::new('email', 'Email'),

            TextField::new('plainPassword', 'Mot de passe')
                ->onlyOnForms()
                ->setRequired($pageName === 'new')
                ->setHelp('Laisse vide pour ne pas modifier le mot de passe'),

            ChoiceField::new('roles', 'Rôle')
                ->allowMultipleChoices()
                ->renderExpanded(false)
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Client' => 'ROLE_CLIENT',
                ]),

            AssociationField::new('company', 'Entreprise')
                ->setRequired(false)
                ->setHelp('Assignation obligatoire pour un client'),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if ($entityInstance->getPlainPassword()) {
                $hashed = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getPlainPassword()
                );
                $entityInstance->setPassword($hashed);
            }
        }

        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if ($entityInstance->getPlainPassword()) {
                $hashed = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getPlainPassword()
                );
                $entityInstance->setPassword($hashed);
            }
        }

        parent::updateEntity($em, $entityInstance);
    }
}