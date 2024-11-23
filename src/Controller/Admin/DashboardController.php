<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Service\ModerationUserService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\StopSSEException;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[IsGranted(AdminUser::ROLE_ADMIN)]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private Packages                 $assetsManager,
        private ModerationUserService    $moderationUserService,
        private AdminUrlGenerator        $routeBuilder,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $links = [
            'Users'      => $routeBuilder->setController(UserCrudController::class)->generateUrl(),
            'Articles'   => $routeBuilder->setController(ArticleCrudController::class)->generateUrl(),
        ];

        $moderationLinks = [
            'Users avatars' => $routeBuilder->setController(ModerationUserAvatarCrudController::class)->generateUrl(),
        ];

        $techLinks = [
            'Mails'           => $routeBuilder->setController(MailCrudController::class)->generateUrl(),
        ];

        return $this->render('admin/index.html.twig',
            [
                'links'              => $links,
                'moderation_links'   => $moderationLinks,
                'tech_links'         => $techLinks,
            ]);

        //return parent::index();
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/homepage', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/', name: 'main')]
    public function main(): Response
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/info', name: 'admin-info')]
    public function info(): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        return $this->render('admin/info.html.twig', ['phpinfo' => $phpinfo]);
    }

    #[Route('/admin/sse', name: 'admin-sse')]
    public function sse(): Response
    {
        return $this->render('admin/sse.html.twig');
    }

    #[Route('/admin/sse-api', name: 'admin-sse-api')]
    public function sseApi(): Response
    {
        return $this->render('admin/sse_api.html.twig');
    }

    #[Route('/admin/sse-events', name: 'admin-sse-events')]
    public function sseEvents(): Response
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering',
            'no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications
        $response->setCallback(function () {
            $callback = function () {
                $id = mt_rand(1, 1000);
                $news = [
                    [
                        'id'      => $id,
                        'title'   => 'title '.$id,
                        'content' => 'content '.$id,
                    ],
                ]; // Get news from database or service.
                if (empty($news)) {
                    return false; // Return false if no new messages
                }
                $shouldStop = false; // Stop if something happens or to clear connection, browser will retry
                if ($shouldStop) {
                    throw new StopSSEException();
                }

                return json_encode(compact('news'));
                // return ['event' => 'ping', 'data' => 'ping data']; // Custom event temporarily: send ping event
                // return ['id' => uniqid(), 'data' => json_encode(compact('news'))]; // Custom event Id
            };
            (new SSE(new Event($callback, 'news')))->start();
        });

        return $response;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Cms');
    }

    public function configureMenuItems(): iterable
    {
        $alertImg = $this->assetsManager->getUrl(sprintf('%s/images/redpoint.gif', $_ENV['CMS_ASSETS_PATH'] ?? ''));

        $moderationUsersAvatarsLabel = 'Users avatars';
        $countUsersWaitModeration = $this->moderationUserService->getCountUsersAvatarsWaitModeration();
        if ($countUsersWaitModeration) {
            $moderationUsersAvatarsLabel = sprintf('<img src="%s" width="10" alt=""> %s',
                $alertImg,
                $moderationUsersAvatarsLabel);
        }

        yield MenuItem::section('Main');

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToUrl(
            'Users',
            'fas fa-users',
            $this->generateControllerUrl(UserCrudController::class),
        )->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToUrl(
            'Articles',
            'fas fa-newspaper',
            $this->generateControllerUrl(ArticleCrudController::class),
        )->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToUrl(
            $moderationUsersAvatarsLabel,
            'fas fa-circle-user',
            $this->generateControllerUrl(ModerationUserAvatarCrudController::class),
        )->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToUrl(
            'Mails',
            'fas fa-envelope',
            $this->generateControllerUrl(MailCrudController::class),
        )->setPermission('ROLE_ADMIN');


        // -->
        yield MenuItem::section('Exit')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }

    private function generateControllerUrl(string $controller): string
    {
        $routeBuilder = $this->routeBuilder
            ->setController($controller)
            ->setAction(Action::INDEX);

        $unsetParams = [
            'query',
            'entityId',
            'page',
            'sort',
        ];

        foreach ($unsetParams as $param) {
            $routeBuilder = $routeBuilder->unset($param);
        }

        return $routeBuilder->generateUrl();
    }
}
