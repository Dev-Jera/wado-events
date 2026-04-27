<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackageEnquiryReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enquiry $enquiry,
        public string  $replyMessage,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Re: Your Enquiry about ' . $this->enquiry->package . ' — WADO Events')
            ->replyTo('wadoconcepts@gmail.com', 'WADO Events')
            ->view('emails.package-enquiry-reply');
    }
}
