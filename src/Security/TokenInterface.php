<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\BaseTokenInterface;

interface TokenInterface extends BaseTokenInterface
{
    /**
     * Returns the token value.
     *
     * @return string The token value
     */
    public function getTokenValue(): string;

    /**
     * Sets the token value.
     *
     * @param string $tokenValue The token value
     */
    public function setTokenValue(string $tokenValue): void;
}