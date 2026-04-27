<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackageEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build(): self
    {
        return $this
            ->subject('New Package Enquiry — ' . $this->data['package'])
            ->replyTo($this->data['email'], $this->data['name'])
            ->view('emails.package-enquiry');
    }
}
