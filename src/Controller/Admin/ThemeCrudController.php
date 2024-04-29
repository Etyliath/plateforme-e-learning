<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield AssociationField::new('programmes')
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('by_reference', false);
        // yield DateTimeField::new('created_at');

    }
}
