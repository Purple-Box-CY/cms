<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserBlock;
use App\Entity\AdminUser;
use App\Service\ModerationUserService;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted(AdminUser::ROLE_ADMIN)]
class ModerationUserAvatarCrudController extends AbstractCrudController
{
    public function __construct(
        private UserService           $userService,
        private ModerationUserService $moderationUserService,
        private AdminUrlGenerator     $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $uid = IdField::new('uid')
            ->setTemplateName('crud/field/id')
            ->setTemplatePath('admin/crud/list/id_url.html.twig')
            ->setCustomOption(AssociationField::OPTION_RELATED_URL, true)
            ->setCustomOption(IdField::OPTION_MAX_LENGTH, 30)
            ->setColumns('col-md-1 col-xxl-1')
            ->setSortable(false)
            ->hideOnForm();

        $id = IntegerField::new('id')
            ->hideOnForm();

        $isVerified = BooleanField::new('is_verified')
            ->renderAsSwitch(false)
        ;

        $isAprooved = BooleanField::new('is_approved')
            ->renderAsSwitch(false)
            ->hideOnIndex()
            ->hideOnForm();

        $isBlocked = BooleanField::new('is_blocked')
            ->renderAsSwitch(false)
            ->hideOnIndex();

        $isDeleted = BooleanField::new('is_deleted')
            ->renderAsSwitch(false)
            ->hideOnIndex();

        $status = ChoiceField::new('status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        User::STATUS_ACTIVE => 'success',
                        User::STATUS_BLOCKED => 'danger',
                        User::STATUS_DELETED => 'dark',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(User::STATUSES, User::STATUSES))
            ->hideOnIndex()
            ->hideOnForm()
        ;

        $approveStatus = ChoiceField::new('approve_status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        User::APPROVE_STATUS_NEED_APPROVE => 'dark',
                        User::APPROVE_STATUS_WAITING_FOR_APPROVE => 'warning',
                        User::APPROVE_STATUS_APPROVED => 'success',
                        User::APPROVE_STATUS_NOT_APPROVED => 'danger',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(User::APPROVE_STATUSES_NAMES, User::APPROVE_STATUSES))
            ->hideOnIndex()
            ->hideOnForm()
        ;


        $avatarStatus = ChoiceField::new('avatar_status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        User::AVATAR_STATUS_ACTIVE => 'success',
                        User::AVATAR_STATUS_BLOCKED => 'danger',
                        User::AVATAR_STATUS_WAITING_APPROVE => 'warning',
                        default => 'light',
                    };
                }
            )
            ->setChoices(array_combine(User::AVAILABLE_AVATAR_STATUSES, User::AVAILABLE_AVATAR_STATUSES))
            ->hideOnIndex()
            ->hideOnForm()
        ;

        $avatarCrop = HiddenField::new('avatarCropUrl', 'Avatar')
            ->setTemplateName('crud/field/image')
            ->addCssClass('field-image')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-image.js'),
                Asset::fromEasyAdminAssetPackage('field-file-upload.js'))
            ->setDefaultColumns('col-md-7 col-xxl-5')
            ->setTextAlign(TextAlign::CENTER)
            //->setMaxLength(1024)
            ->hideOnForm()
            ->hideOnDetail()
        ;

        $avatar = HiddenField::new('avatarOriginalUrl', 'Avatar')
            ->setTemplateName('crud/field/image')
            ->addCssClass('field-image')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-image.js'),
                Asset::fromEasyAdminAssetPackage('field-file-upload.js'))
            ->setDefaultColumns('col-md-7 col-xxl-5')
            ->setTextAlign(TextAlign::CENTER)
            //->setMaxLength(1024)
            //->hideOnForm()
            ->hideOnIndex()
        ;

        //$avatarUpload = ImageField::new('image_file')
        //    ->setUploadDir($this->userService->getUserAvatarUploadDir())
        //    ->setUploadedFileNamePattern('[randomhash].[extension]')
        //    ->hideOnIndex()
        //    ->hideOnDetail();

        //$status = ChoiceField::new('status');

        $info = AssociationField::new('info')
            ->setLabel('Other info:')
            ->renderAsEmbeddedForm(UserInfoController::class)
            ->hideOnIndex()
            ->hideOnDetail();

        $approveActions = ArrayField::new('id')
            ->setLabel('Actions:')
            ->setTemplatePath('admin/moderation/user/approve_avatar_actions.html.twig')
            ->hideOnIndex()
            ->hideOnForm();

        $declineReasons = ArrayField::new('avatarDeclineReason')
            ->setLabel('Decline reason:')
            ->setTemplatePath('admin/moderation/user/decline_reasons.html.twig')
            ->hideOnIndex()
            ->hideOnForm();

        $declineDescription = TextareaField::new('avatarDeclineDescription')
            ->setLabel('Decline note:')
            ->setTemplatePath('admin/moderation/user/decline_description.html.twig')
            ->hideOnIndex()
            ->hideOnForm();

        $fullName = TextField::new('full_name')
            ->hideOnIndex();

        return [
            FormField::addColumn(5),
            $avatar,
            $avatarCrop,
            //$avatarUpload,
            $uid,
            $id,
            TextField::new('email'),
            TextField::new('username'),
            $fullName,
            //$bioLink,
            //$status,
            $isVerified,
            $isAprooved,
            $isBlocked,
            $isDeleted,
            $status,
            $approveStatus,
            $avatarStatus,
            $info,
            FormField::addColumn(5),
            $approveActions,
            $declineReasons,
            $declineDescription,
            //$blockReasons,
        ];
    }

    #[Route('/user/avatar/approve', name: 'user_avatar_approve')]
    public function approveAction(int $id, Request $request)
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('User %s not found', $id));
        }

        $this->moderationUserService->approveUserAvatar($user);

        $this->addFlash('success', '<span style="color: green"><i class="fa fa-check"></i> User avtar approved</span>');

        $url = $this->adminUrlGenerator
            ->setController(ModerationUserAvatarCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/user/avatar/decline', name: 'user_avatar_decline')]
    public function declineAction(int $id, Request $request)
    {
        $declineReason = $request->query->get('decline_reason');
        $declineDescription = $request->query->get('decline_description');

        $user = $this->userService->getUserById($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('User %s not found', $id));
        }

        $this->moderationUserService->declineUserAvatar(
            user: $user,
            declineReason: $declineReason,
            declineDescription: $declineDescription,
        );

        $this->addFlash('success', '<span style="color: green"><i class="fa fa-check"></i> User avatar declined</span>');

        $url = $this->adminUrlGenerator
            ->setController(ModerationUserAvatarCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }

    public function createIndexQueryBuilder(
        SearchDto        $searchDto,
        EntityDto        $entityDto,
        FieldCollection  $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->where(sprintf("entity.avatarStatus = '%s'", User::AVATAR_STATUS_WAITING_APPROVE));

        return $queryBuilder;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Moderation users avatars')
            ->setEntityLabelInPlural('Moderation users avatars')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(null)
            ;
    }

    public function detail(AdminContext $context)
    {
        $responseParameters = parent::detail($context);

        if ($responseParameters->get('pageName') == Crud::PAGE_DETAIL) {
            /** @var EntityDto $entity */
            $entity = $responseParameters->get('entity');

            $declineReasons = UserBlock::REASONS_NAMES;
            $responseParameters->set('decline_reasons', $declineReasons);
            $this->container->get('twig')->addGlobal('decline_reasons', $declineReasons);
        }

        return $responseParameters;
    }
}
