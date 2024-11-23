<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(AdminUser::ROLE_ADMIN)]
class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    //public function configureCrud(Crud $crud): Crud
    //{
    //    return $crud
    //        ->setDefaultSort(['isActive' => 'DESC', 'rank' => 'ASC']);
    //}

    public function configureFields(string $pageName): iterable
    {
        //$id = IdField::new('id')
        //    ->setTemplateName('crud/field/id')
        //    ->setTemplatePath('admin/crud/list/id_url.html.twig')
        //    ->setCustomOption(AssociationField::OPTION_RELATED_URL, true)
        //    ->setCustomOption(IdField::OPTION_MAX_LENGTH, 30)
        //    ->setColumns('col-md-1 col-xxl-1')
        //    ->setSortable(false)
        //    ->hideOnDetail()
        //    ->hideOnForm();

        $isActive = BooleanField::new('is_active')
            ->renderAsSwitch(true)
            //->hideOnForm()
        ;

        $id = NumberField::new('id')->onlyOnDetail();

        //$alias = TextField::new('alias')->setRequired(true);
        $alias = IdField::new('alias')
            ->setTemplateName('crud/field/id')
            ->setTemplatePath('admin/crud/list/id_url.html.twig')
            ->setCustomOption(AssociationField::OPTION_RELATED_URL, true)
            ->setCustomOption(IdField::OPTION_MAX_LENGTH, 30)
            //->setColumns('col-md-1 col-xxl-1')
            ->setSortable(false)
            ->setRequired(true)
            //->hideOnDetail()
            //->hideOnForm()
        ;

        $title = TextField::new('title')->setRequired(true);
        //$description = TextareaField::new('description')->hideOnIndex();
        $createdAt = DateTimeField::new('created_at')
            ->hideOnIndex()
            ->hideOnForm();
        $updatedAt = DateTimeField::new('updated_at')
            ->hideOnIndex()
            ->hideOnForm();


        $description = TextEditorField::new('description')
            ->setTrixEditorConfig([
                'blockAttributes' => [
//                    'default' => ['tagName' => 'p'],
                    'heading1' => ['tagName' => 'h2'],
                ]
            ])
            ->setLabel('Description')
            ->hideOnDetail()
            ->hideOnIndex();

        $descriptionHtml = TextareaField::new('description')
            ->setLabel('Description')
            ->renderAsHtml()
            ->hideOnForm()
            ->hideOnIndex();


        return [
            $id,
            $alias,
            $title,
            $isActive,
            $createdAt,
            $updatedAt,
            $description,
            $descriptionHtml,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }
}
