<?php

namespace App\Controller\Admin;

use App\Entity\Mail\Mail;
use App\Entity\AdminUser;
use App\Entity\Mail\MailType;
use App\Service\Infrastructure\Mail\MailService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted(AdminUser::ROLE_ADMIN)]
class MailCrudController extends AbstractCrudController
{
    public function __construct(
        private MailService           $mailService,
        private AdminUrlGenerator     $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Mail::class;
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

        $idLink = IdField::new('id')
            ->setTemplateName('crud/field/id')
            ->setTemplatePath('admin/crud/list/id_url.html.twig')
            ->setCustomOption(AssociationField::OPTION_RELATED_URL, true)
            ->setCustomOption(IdField::OPTION_MAX_LENGTH, 30)
            ->setColumns('col-md-1 col-xxl-1')
            ->setSortable(false)
            ->hideOnDetail()
            ->hideOnForm();

        $emailTo = TextField::new('email_to');
        $subject = TextField::new('subject');
        $template = TextField::new('template')
            ->hideOnIndex();

        $context = TextareaField::new('contextJson', 'Context')
            ->setTemplatePath('admin/crud/field/json.html.twig')
            ->hideOnIndex()
        ;

        $error = TextField::new('error')
            ->hideOnIndex();

        $type = TextField::new('type');

        $status = ChoiceField::new('status')
            ->renderAsBadges(
                static function ($field): string {
                    return match ($field) {
                        Mail::STATUS_NEW => 'primary',
                        Mail::STATUS_ERROR => 'danger',
                        Mail::STATUS_SUCCESS => 'success',
                        default => 'light',
                    };
                },
            )
            ->setChoices(array_combine(Mail::AVAILABLE_STATUSES, Mail::AVAILABLE_STATUSES));


        $createdAt = DateTimeField::new('created_at')
            ->hideOnForm();
        $sentAt = DateTimeField::new('sent_at')
            ->hideOnForm();

        return [
            //$id,
            $idLink,
            $emailTo,
            $subject,
            $template,
            $context,
            $error,
            $type,
            $status,
            $createdAt,
            $sentAt,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $resendButton = Action::new('sendAction', 'ReSend', 'fa fa-envelope')
            ->linkToRoute('mail_send', function (Mail $mail): array {
                return [
                    'id' => $mail->getId(),
                ];
            });

        return $actions
            ->add(Crud::PAGE_DETAIL, $resendButton)
            ->add(Crud::PAGE_INDEX, $resendButton)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }

    #[Route('/mail/send', name: 'mail_send')]
    public function sendAction(int $id, Request $request)
    {
        $mail = $this->mailService->getMailById($id);
        if (!$mail) {
            throw new NotFoundHttpException(sprintf('Mail %s not found', $id));
        }

        $url = $this->adminUrlGenerator
            ->setController(MailCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        try {
            $this->mailService->addMailToQueue($mail);
        } catch (\Throwable $e) {
            $this->addFlash('error', '<span style="color: red"><i class="fa fa-check"></i> Failed to add mail to queue. Error: '.$e->getMessage().'</span>');

            return $this->redirect($url);
        }
        $this->addFlash('success', '<span style="color: green"><i class="fa fa-check"></i> Mail was successfully added to the queue for sending</span>');

        return $this->redirect($url);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $statusFilter = ChoiceFilter::new('status')
            ->setChoices(Mail::AVAILABLE_STATUSES);

        $typeFilter = ChoiceFilter::new('type')
            ->setChoices(MailType::AVAILABLE_TYPES);

        $sentAt = DateTimeFilter::new('sentAt');

        return $filters
            ->add($statusFilter)
            ->add($typeFilter)
            ->add($sentAt)
            ;
    }
}
