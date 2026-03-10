<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;


class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),

            TextField::new('name', 'Nom de l’entreprise')
                ->setHelp('Nom affiché dans le dashboard client')
                ->setFormTypeOption('attr', [
                    'placeholder' => 'Ex : Auxerre Informatique',
                ]),

            UrlField::new('website', 'Site web')
                ->setHelp('Inclure https://')
                ->setFormTypeOption('attr', [
                    'placeholder' => 'https://exemple.com',
                ]),

            TextareaField::new('context', 'Contexte IA')
                ->setNumOfRows(10)
                ->setHelp('Décrivez le contexte utilisé par l’IA pour cette entreprise.')
                ->setFormTypeOption('attr', [
                    'style' => 'resize: vertical; border-radius: 12px;',
                    'placeholder' => 'Ex : L’entreprise vend des produits artisanaux...',
                ]),

            DateTimeField::new('createdAt', 'Créée le')
                ->hideOnForm(),
        ];
    }

   
            

   
}