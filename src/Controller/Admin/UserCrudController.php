<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\AdminUser;
use App\Entity\UserBlock;
use App\Service\AuthService;
use App\Service\UserService;
use App\Service\Utility\DomainHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[IsGranted(AdminUser::ROLE_ADMIN)]
class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserService    $userService,
        private AuthService    $authService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
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

        $id = IntegerField::new('id')
            ->hideOnForm();

        $fullName = TextField::new('full_name')//->hideOnIndex()
        ;

        $newPassword = TextField::new('newPassword', 'New password')
            ->hideOnIndex()
            ->hideOnDetail();

        $createdAt = DateTimeField::new('createdAt')
            //->hideOnIndex()
            ->hideOnForm();

        $lastActivity = DateTimeField::new('lastActivityAt', 'Last activity')
            ->setSortable(true)
            ->hideOnForm();

        $team = AssociationField::new('team')
            ->hideOnIndex();

        $source = TextField::new('source')
            ->hideOnIndex()
            ->hideOnForm();
        $googleId = TextField::new('googleId')
            ->hideOnIndex()
            ->hideOnForm();
        $facebookId = TextField::new('facebookId')
            ->hideOnIndex()
            ->hideOnForm();

        $isVerified = BooleanField::new('is_verified')
            ->renderAsSwitch(false);


        $isBlocked = BooleanField::new('is_blocked')
            ->renderAsSwitch(false)
            ->hideOnIndex()
            ->hideOnForm();

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
            ->hideOnForm();

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
            ->setChoices(array_combine(User::APPROVE_STATUSES_NAMES, User::APPROVE_STATUSES));

        $avatarStatus = ChoiceField::new('avatar_status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        User::AVATAR_STATUS_ACTIVE => 'success',
                        User::AVATAR_STATUS_BLOCKED => 'danger',
                        User::AVATAR_STATUS_WAITING_APPROVE => 'warning',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(User::AVAILABLE_AVATAR_STATUSES, User::AVAILABLE_AVATAR_STATUSES))
            ->hideOnIndex()
            ->hideOnForm();

        $blockUser = HiddenField::new('id')
            ->setTemplatePath('admin/moderation/user/block_user.html.twig')
            ->setLabel('Actions')
            ->hideOnIndex()
            ->hideOnForm()
            ->hideOnIndex();

        $blockUserReasons = ArrayField::new('blockReason')
            ->setLabel('Block reason:')
            ->setTemplatePath('admin/moderation/user/block_reasons.html.twig')
            ->hideOnIndex()
            ->hideOnForm();

        $declineReason = TextareaField::new('declineReasonStr')
            ->setLabel('Decline reason')
            ->hideOnIndex()
            ->hideOnForm();

        $declineDescription = TextareaField::new('declineDescriptionStr')
            ->setLabel('Decline description')
            ->hideOnIndex()
            ->hideOnForm();

        $avatarDeclineReason = TextareaField::new('avatarDeclineReasonName')
            ->setLabel('Avatar decline reason')
            ->hideOnIndex()
            ->hideOnForm();

        $avatarDeclineDescription = TextareaField::new('avatarDeclineDescription')
            ->setLabel('Decline description')
            ->hideOnIndex()
            ->hideOnForm();

        $avatar = HiddenField::new('avatarUrl', 'Avatar')
            ->setTemplateName('crud/field/image')
            ->addCssClass('field-image')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-image.js'),
                Asset::fromEasyAdminAssetPackage('field-file-upload.js'))
            ->setDefaultColumns('col-md-7 col-xxl-5')
            ->setTextAlign(TextAlign::CENTER)
            //->setMaxLength(1024)
            //->hideOnForm()
        ;

        $avatarOriginal = HiddenField::new('avatarOriginalUrl', 'Avatar original')
            ->setTemplateName('crud/field/image')
            ->addCssClass('field-image')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-image.js'),
                Asset::fromEasyAdminAssetPackage('field-file-upload.js'))
            ->setDefaultColumns('col-md-7 col-xxl-5')
            ->setTextAlign(TextAlign::CENTER)
            //->setMaxLength(1024)
            ->hideOnForm()
            ->hideOnIndex();


        $avatarUpload = ImageField::new('image_file')
            ->setUploadDir($this->userService->getUserAvatarUploadDir())
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->hideOnIndex()
            ->hideOnDetail();

        $isUnsubscribed = BooleanField::new('isUnsubscribed')
            ->setLabel('Notifications Unsubscribed')
            ->hideOnIndex()
            ->hideOnForm()
            ->renderAsSwitch(false);

        return [
            FormField::addTab('Main'),
            FormField::addColumn(6),
            $avatar,
            $avatarOriginal,

            //$avatarUpload,
            $ulid,
            $uid,
            $id,
            TextField::new('email'),
            TextField::new('username'),
            $fullName,
            $newPassword,
            $team,
            $source,
            $googleId,
            $facebookId,
            //$bioLink,
            //$status,
            $isVerified,
            $isDeleted,
            $isBlocked,
            $lastActivity,
            $createdAt,
            $status,
            $approveStatus,
            $avatarStatus,
            $avatarDeclineReason,
            $avatarDeclineDescription,

            $declineReason,
            $declineDescription,
            $isUnsubscribed,
            FormField::addColumn(6),
            $blockUser,
            $blockUserReasons,

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $loginButton = Action::new('loginAction', 'Login', 'fa fa-sign-in')
            ->setHtmlAttributes(['target' => '_blank'])
            ->linkToRoute('user_login',
                function (User $object): array {
                    return [
                        'id' => $object->getId(),
                    ];
                })
            ->displayIf(fn(User $object) => !$object->isDeleted());

        return $actions
            ->add(Crud::PAGE_DETAIL, $loginButton)
            ->add(Crud::PAGE_INDEX, $loginButton)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    #[Route('/user/login', name: 'user_login')]
    public function reuploadAction(int $id, Request $request)
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('User %s not found', $id));
        }

        $hash = $this->authService->getAuthHashByUser($user);
        if (!$hash) {
            throw new \Exception('Failed to get uaser auth hash from api');
        }

        $route = sprintf(AuthService::ROUTE_FRONT_AUTH_HASH, $hash, AuthService::AUTH_TYPE_CMS);

        return new RedirectResponse(DomainHelper::getWebProjectDomain().$route);
    }

    public function detail(AdminContext $context)
    {
        $responseParameters = parent::detail($context);

        if ($responseParameters->get('pageName') == Crud::PAGE_DETAIL) {
            /** @var EntityDto $entity */
            $entity = $responseParameters->get('entity');

            /** @var User $user */
            $user = $entity->getInstance();

            $blockReasons = UserBlock::REASONS_NAMES;
            $responseParameters->set('block_reasons', $blockReasons);
            $this->container->get('twig')->addGlobal('block_reasons', $blockReasons);
            $this->container->get('twig')->addGlobal('is_blocked', $user->isBlocked());

            $this->container->get('twig')->addGlobal('user', $user);

        }

        return $responseParameters;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $sourceFilter = ChoiceFilter::new('source')
            ->setChoices(array_combine(User::SOURCES, USER::SOURCES));
        $isAnonFilter = BooleanFilter::new('isAnonym');
        $isDeletedFilter = BooleanFilter::new('isDeleted');
        $isVerifiedFilter = BooleanFilter::new('isVerified');
        $isApprovedFilter = BooleanFilter::new('isApproved');
        $isPayingFilter = BooleanFilter::new('isPaying');
        $isTalkerFilter = BooleanFilter::new('isTalker');
        $teamFilter = EntityFilter::new('team');
        //$createdAt = DateTimeFilter::new('createdAt');
        $lastActivityAt = DateTimeFilter::new('lastActivityAt');

        return $filters
            ->add($sourceFilter)
            ->add($isAnonFilter)
            ->add($isVerifiedFilter)
            ->add($isApprovedFilter)
            ->add($isPayingFilter)
            ->add($isTalkerFilter)
            ->add($isDeletedFilter)
            ->add($teamFilter)
            ->add($lastActivityAt);
    }


    public function delete(AdminContext $context)
    {
        $context->getRequest()->query->set(EA::REFERRER, null);

        return parent::delete($context);
    }

}
