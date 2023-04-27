<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\Mapping\Id;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // disable add button
    public function configureActions(Actions $actions): Actions
    {
        return $actions->remove(Crud::PAGE_INDEX, Action::NEW);
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('firstname')->setLabel('Prénom');
        yield TextField::new('lastname')->setLabel('Nom');
        yield TextField::new('email');
        yield TextField::new('siren');
        yield BooleanField::new('approved')->setLabel('Approuvé');
        yield TextField::new('password')->onlyWhenCreating()->setLabel('Mot de passe');
        yield ChoiceField::new('roles')->setChoices([
            'Utilisateur' => 'ROLE_USER',
            'Administrateur' => 'ROLE_ADMIN',
            'Commerçant' => 'ROLE_MERCHANT',
        ])->setLabel('Rôle')->hideWhenCreating()->allowMultipleChoices();
        yield AssociationField::new('commerce')->setLabel('Commerce')->hideWhenCreating();
        yield IntegerField::new('loyalty_points')->setLabel('Points de fidélité');
    }
}
