<?php

namespace App\Controller\Admin;

use App\Entity\Trader;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TraderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trader::class;
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
