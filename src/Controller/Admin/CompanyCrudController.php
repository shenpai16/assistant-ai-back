<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;


class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('name', 'Nom de l\'entreprise'),

            UrlField::new('website', 'Site web')
                ->setHelp('Entrez l\'URL complète, y compris https://'),

            TextareaField::new('context', 'Contexte IA')
                ->setNumOfRows(12)
                ->setHelp('Décrivez le contexte de l\'IA pour cette entreprise.'),

            DateTimeField::new('created_at', 'Date de création')
                ->hideOnForm(),
        ];
    }
    
}
