<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Marker;
use App\Service\ImageService;
use App\Service\Utility\DomainHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(AdminUser::ROLE_ADMIN)]
class MarkerCrudController extends AbstractCrudController
{
    public function __construct(
        private ImageService $imageService,
    ) {}
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

        $coordinates = TextField::new('coordinates')
            ->hideOnForm()
            ->hideOnDetail()
        ;

        $status = ChoiceField::new('status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        Marker::STATUS_ACTIVE => 'success',
                        Marker::STATUS_WAITING_APPROVE => 'primary',
                        Marker::STATUS_DELETED => 'danger',
                        Marker::STATUS_BLOCKED => 'danger',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(Marker::AVAILABLE_STATUSES, Marker::AVAILABLE_STATUSES));

        $type = ChoiceField::new('type')
            ->setChoices(Marker::NAMES_TYPES);

        $location = ChoiceField::new('location')
            ->setChoices(Marker::NAMES_LOCATIONS);

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

        $name = TextField::new('name')
            ->hideOnIndex()
        ;

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

        $imageUrl = TextField::new('imageUrl')
            ->hideOnIndex()
            ->hideOnDetail()
        ;

        $createdAt = DateTimeField::new('created_at')
            ->hideOnIndex()
            ->hideOnForm();
        $updatedAt = DateTimeField::new('updated_at')
            ->hideOnIndex()
            ->hideOnForm();


        $image = HiddenField::new('imageUrl')
            ->setTemplateName('crud/field/image')
            ->addCssClass('field-image')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-image.js'), Asset::fromEasyAdminAssetPackage('field-file-upload.js'))
            ->setDefaultColumns('col-md-7 col-xxl-5')
            ->setTextAlign(TextAlign::CENTER)
            //->setMaxLength(1024)
            //->hideOnForm()
        ;

        $imageUpload = ImageField::new('image_file')
            ->setUploadDir($this->imageService->getMarkersImageUploadDir())
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->hideOnIndex()
            ->hideOnDetail();

        return [
            $image,
            $imageUpload,
            $id,
            $ulid,
            $uid,
            $latitude,
            $longitude,
            $status,
            $location,
            $type,
            $isActive,
            $isPaper,
            $isGlass,
            $isPlastic,
            $isCloth,
            $isElectronic,
            $isBattery,
            $coordinates,
            $name,
            $shortDescription,
            $description,
            //$createdAt,
            //$updatedAt,
            $description,
            $descriptionHtml,
            //$imageUrl,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $showPageButton = Action::new('showPageAction', 'Show on map', 'fa fa-map-marker')
            ->setHtmlAttributes(['target' => '_blank'])
            ->linkToRoute('marker_show_page',
                function (Marker $object): array {
                    return [
                        'uid' => $object->getUidStr(),
                    ];
                })
            ->displayIf(fn(Marker $object) => !$object->isDeleted());

        return $actions
            ->add(Crud::PAGE_DETAIL, $showPageButton)
            ->add(Crud::PAGE_INDEX, $showPageButton)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }


    #[Route('/marker/show-page', name: 'marker_show_page')]
    public function showPageAction(string $uid, Request $request): RedirectResponse
    {
        return new RedirectResponse(DomainHelper::getWebProjectDomain().'/'.$uid);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $typeFilter = ChoiceFilter::new('type')
            ->setChoices(Marker::NAMES_TYPES);

        $statusFilter = ChoiceFilter::new('status')
            ->setChoices(array_combine(Marker::AVAILABLE_STATUSES, Marker::AVAILABLE_STATUSES));


        $locationFilter = ChoiceFilter::new('location')
            ->setChoices(Marker::NAMES_LOCATIONS);

        return $filters
            ->add($typeFilter)
            ->add($statusFilter)
            ->add($locationFilter)
            ;
    }
}
