<?php

namespace App\Repository\Traits;

trait EntityManagerTrait
{
    protected function checkEm(): void
    {
        if (!$this->_em->isOpen()) {
            $this->_em = $this->_em->create(
                $this->_em->getConnection(),
                $this->_em->getConfiguration()
            );
        }
    }
}