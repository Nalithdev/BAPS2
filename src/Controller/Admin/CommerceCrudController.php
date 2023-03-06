<?php

namespace App\Controller\Admin;

use App\Entity\Commerce;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommerceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commerce::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
