<?php

declare(strict_types=1);

namespace App\Asset;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * taken from: https://github.com/EasyCorp/EasyAdminBundle/issues/5382#issuecomment-1229448568.
 */
class EasyAdminAssetsPackage implements PackageInterface
{
    private PackageInterface $package;

    public function __construct(RequestStack $requestStack, string $basePath)
    {
        $this->package = new PathPackage(
            $basePath.'/bundles/easyadmin',
            new JsonManifestVersionStrategy(__DIR__.'/../../public/bundles/easyadmin/manifest.json'),
            new RequestStackContext($requestStack)
        );
    }

    public function getUrl(string $path): string
    {
        return $this->package->getUrl($path);
    }

    public function getVersion(string $path): string
    {
        return $this->package->getVersion($path);
    }
}