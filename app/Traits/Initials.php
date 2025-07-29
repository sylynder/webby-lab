<?php

namespace App\Traits;

use function PHPUnit\Framework\isEmpty;

trait Initials
{
    public function getInitials(string $firstName, string $lastName = ''): string
    {
        if (empty($firstName)) {
            return "The name is necessary";
        }

        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if (mb_strpos($firstName, ' ')) {
            $nameArr = explode(' ', $firstName);
            return $this->getInitialsFullName($nameArr[0], $nameArr[1]);
        }

        return $this->getInitialsFirstName($firstName);
    }

    private function getInitialsFirstName(string $firstName): string
    {
        $initial = mb_substr($firstName, 0, 1);
        return mb_strtoupper($initial);
    }

    private function getInitialsFullName(string $firstName, string $lastName): string
    {
        $initials = mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1);
        return mb_strtoupper($initials);
    }
}

