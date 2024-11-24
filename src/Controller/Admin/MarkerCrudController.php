<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Marker;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(AdminUser::ROLE_ADMIN)]
class MarkerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Marker::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id')
            ->hideOnForm();

        $ulid = IdField::new('uid')
            ->setTemplateName('crud/field/id')
            ->setTemplatePath('admin/crud/list/id_url.html.twig')
            ->setCustomOption(AssociationField::OPTION_RELATED_URL, true)
            ->setCustomOption(IdField::OPTION_MAX_LENGTH, 30)
            ->setColumns('col-md-1 col-xxl-1')
            ->setSortable(false)
            ->hideOnDetail()
            ->hideOnForm();

        $uid = TextField::new('uid')
            ->hideOnIndex()
            ->hideOnForm();

        $latitude = TextField::new('latitude')
            ->setLabel('latitude')
            ->hideOnIndex()
        ;
        $longitude = TextField::new('longitude')
            ->setLabel('longitude')
            ->hideOnIndex()
        ;

        $status = ChoiceField::new('status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        Marker::STATUS_ACTIVE => 'success',
                        Marker::STATUS_DELETED => 'danger',
                        Marker::STATUS_BLOCKED => 'danger',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(Marker::AVAILABLE_STATUSES, Marker::AVAILABLE_STATUSES));

        $type = ChoiceField::new('type')
            ->setChoices(Marker::NAMES_TYPES);

        $isActive = BooleanField::new('is_active')
            ->hideOnIndex()
            ->renderAsSwitch(false)
            ->hideOnForm()
        ;

        $isPaper = BooleanField::new('isPaper')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $isGlass = BooleanField::new('isGlass')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $isPlastic = BooleanField::new('isPlastic')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $isCloth = BooleanField::new('isCloth')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $isElectronic = BooleanField::new('isElectronic')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $isBattery = BooleanField::new('isBattery')
            ->hideOnIndex()
            ->renderAsSwitch(false)
        ;

        $name = TextField::new('name');

        $shortDescription = TextareaField::new('shortDescription')
            ->setLabel('Short description')
            //->hideOnForm()
            ->hideOnIndex();


        //$description = TextareaField::new('description')->hideOnIndex();

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

        $createdAt = DateTimeField::new('created_at')
            ->hideOnIndex()
            ->hideOnForm();
        $updatedAt = DateTimeField::new('updated_at')
            ->hideOnIndex()
            ->hideOnForm();

        return [
            $id,
            $ulid,
            $uid,
            $latitude,
            $longitude,
            $status,
            $type,
            $isActive,
            $isPaper,
            $isGlass,
            $isPlastic,
            $isCloth,
            $isElectronic,
            $isBattery,
            $name,
            $shortDescription,
            $description,
            //$createdAt,
            //$updatedAt,
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

    public function configureFilters(Filters $filters): Filters
    {
        $typeFilter = ChoiceFilter::new('type')
            ->setChoices(Marker::NAMES_TYPES);

        $statusFilter = ChoiceFilter::new('status')
            ->setChoices(array_combine(Marker::AVAILABLE_STATUSES, Marker::AVAILABLE_STATUSES));

        return $filters
            ->add($typeFilter)
            ->add($statusFilter)
            ;
    }
}
