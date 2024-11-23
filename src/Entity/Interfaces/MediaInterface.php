<?php

namespace App\Entity\Interfaces;

interface MediaInterface
{
    public function isLocalStorage(): bool;

    public function isCDNStorage(): bool;

    public function isConverted(): bool;

    public function getPath(): ?string;

    public function setStatusConvertInProcess(): self;

    public function getUidStr(): string;

    public function getPathPreview(): ?string;

    public function getPreviewUrl(): ?string;

    public function getType(): ?string;

    public function getPathPreviewBlur(): ?string;

    public function getPreviewBlurUrl(): ?string;
}