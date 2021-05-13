<?php


namespace App\Security;


use Symfony\Component\Security\Core\Exception\AccountStatusException;

class ContestLoginException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return 'You must be a customer in order to login.';
    }

}