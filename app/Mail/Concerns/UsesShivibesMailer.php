<?php

namespace App\Mail\Concerns;

trait UsesShivibesMailer
{
    protected function shivibesFrom(): array
    {
        return [
            config('shivibes.mail.noreply_address'),
            config('shivibes.mail.from_name'),
        ];
    }

    protected function supportReplyTo(): array
    {
        return [config('shivibes.mail.support_address')];
    }
}
