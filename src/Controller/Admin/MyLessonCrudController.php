<?php

namespace App\Controller\Admin;

use App\Entity\MyLesson;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class MyLessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MyLesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user');
        yield AssociationField::new('lesson');
        yield BooleanField::new('purchased');
        yield BooleanField::new('validated');
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
