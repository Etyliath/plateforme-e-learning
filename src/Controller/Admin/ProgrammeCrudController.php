<?php

namespace App\Controller\Admin;

use App\Entity\Programme;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProgrammeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Programme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('theme');
        yield TextField::new('name');
        yield IntegerField::new('price');
        yield AssociationField::new('lessons');
    }
}
