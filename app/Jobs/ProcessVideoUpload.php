<?php

namespace App\Jobs;

use Throwable;
use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use App\Services\ProcessVideo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = 86400;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Lesson $lesson){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ProcessVideo::execute($this->lesson);
    }

    public function failed(Throwable $th){
        if($th->getCode() === 403){
            $this->release();
        }else {
            info($th->getMessage());
        }
    }
}
