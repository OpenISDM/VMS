<?php

namespace App\Jobs;

use App\Volunteer;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerificationEmail extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $volunteer;
    protected $verificationCode;
    protected $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Volunteer $volunteer, $verificationCode, $subject)
    {
        $this->volunteer = $volunteer;
        $this->verificationCode = $verificationCode;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        if ($this->attempts() < 10) {
            $verificationUrl = $this->verificationUrlStringBuilder($this->volunteer->email, $this->verificationCode);
            $data = [
                'name'            => $this->volunteer->last_name,  // the volunteer's name
                'verificationUrl' => $verificationUrl,   // the URL for email verification
            ];

            $lastName = $this->volunteer->last_name;
            $emailAddress = $this->volunteer->email;
            $subject = $this->subject;

            $mailer->send(
                ['html' => 'emails.verify'],
                $data,
                function ($message) use ($lastName, $emailAddress, $subject) {
                    $message->to($emailAddress, $lastName)->subject($subject);
                }
            );
        }
    }

    /**
     * For verification url string builder.
     *
     * @param string $email
     * @param string $verificationCode
     *
     * @return string
     */
    private function verificationUrlStringBuilder($email, $verificationCode)
    {
        $url = config('vms.emailVerificationUrl');
        $url .= '?email='.rawurlencode($email);
        $url .= '&verification_token='.rawurlencode($this->verificationCode);

        return $url;
    }
}
