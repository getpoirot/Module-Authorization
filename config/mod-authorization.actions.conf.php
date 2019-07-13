<?php
use Module\Authorization\Actions;

return [
    'services' => [
        Actions::Authenticator => Actions\AuthenticatorAction::class,
    ],
];
