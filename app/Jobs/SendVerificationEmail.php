<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use App\Volunteer;

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
            $data = [
                    'name' => $this->volunteer->last_name,
                    'verifcationUrl' => $this->verificationCode
                ];
            $lastName = $this->volunteer->last_name;
            $emailAddress = $this->volunteer->email;
            $subject = $this->subject;

            $mailer->send('emails.verify',
                            $data, 
                            function ($message) use ($lastName, $emailAddress, $subject) {
                                $message->to($emailAddress, $lastName)->subject($subject);
                        });
        }
    }
}
