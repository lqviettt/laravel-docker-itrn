<?php

namespace App\Jobs;

use App\Mail\OrderSuccessfulMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    public $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing order', ['order' => $this->order]);
            Mail::to($this->order->customer_email)->send(new OrderSuccessfulMail($this->order));
            Log::info('Email sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to process job', ['error' => $e->getMessage(), 'order' => $this->order->toArray()]);
            throw $e;
        }
    }
}
