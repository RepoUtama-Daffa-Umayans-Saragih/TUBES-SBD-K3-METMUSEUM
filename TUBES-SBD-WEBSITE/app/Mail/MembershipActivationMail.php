<?php
namespace App\Mail;

use App\Models\Membership;
use Illuminate\Mail\Mailable;

class MembershipActivationMail extends Mailable
{
    public function __construct(public Membership $membership)
    {
    }

    public function build()
    {
        return $this->subject('Activate Your MET Membership')
            ->view('emails.membership-activation')
            ->with([
                'membership'    => $this->membership,
                'activationUrl' => route('member.activate', $this->membership->activation_token),
            ]);
    }
}
