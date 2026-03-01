<?php

namespace App\Controller\Admin;

use App\Entity\Conversation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;



class ConversationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conversation::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            yield FormField::addTab('Conversation'),

            yield IdField::new('id')->hideOnForm(),

            yield AssociationField::new('company', 'Entreprise')
                ->setRequired(true),

            yield TextField::new('sessionId', 'Session ID')
                ->setHelp('Identifiant unique de la session utilisateur'),

            yield DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),

            yield FormField::addTab('Messages'),

            yield TextareaField::new('messagesView', 'Historique')
                        ->setTemplatePath('admin/conversation/messages_view.html.twig'),

        ];

    }
    
}
