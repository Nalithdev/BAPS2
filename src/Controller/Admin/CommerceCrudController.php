<?php

namespace App\Controller\Admin;

use App\Entity\Commerce;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommerceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commerce::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('description'),
			AssociationField::new('owner'),
			TextField::new('adresse'),
        ];
    }
}
