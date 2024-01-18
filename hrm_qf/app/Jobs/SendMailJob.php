<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $subject;
    public $name;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $name, $body)
    {
        $this->email   = $email;
        $this->subject = $subject;
        $this->name    = $name;
        $this->body    = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new SendMail($this->subject, $this->name, $this->body));
    }
}
