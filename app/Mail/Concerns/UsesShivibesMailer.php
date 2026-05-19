<?php
namespace App\Mail\Concerns;

use Illuminate\Mail\Mailables\Address;

trait UsesShivibesMailer
{
    protected function shivibesFrom(): Address
    {
        return new Address(
            config('mail.from.address'),
            config('mail.from.name')
        );
    }
	protected function supportReplyTo(): Address
    {
		return new Address(
		config('shivibes.mail.support_address')
        );
    }
}
