<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasUuid;
use App\Mail\SendMagicLinkToUser;
use Laravel\Sanctum\HasApiTokens;
use App\Services\MagicLinkService;
use App\Mail\SendPasswordResetMail;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuid, HasApiTokens;

    protected $guard = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'cv_details',
        'github_link',
        'access_to_laptop',
        'current_education_level',
        'phonenumber',
        'notification_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function magictokens()
    {
        return $this->morphMany(MagicToken::class, 'tokenable');
    }

    public function sendMagicLink(){
        $data =  MagicLinkService::createToken($this);

        Mail::to($this->email)->queue(new SendMagicLinkToUser($data['token'], $data['expires_at'],  $this));
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.front.url') . '/forgotpassword/resetpassword?token=' . $token.'&email='.$this->email;

        Mail::to($this->email)->queue(new SendPasswordResetMail($url));
    }

    public function submissions(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Submission::class, 'taskable');
    }

    public function schedule(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Schedule::class, 'schedulable');
    }

    public function attendance(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Attendance::class, 'attender');
    }

    public function settings(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Setting::class, 'changeable');
    }

    public function completedTasks(): \Illuminate\Support\Collection
    {
        $tasks  = collect(json_decode($this->submissions->tasks, true));

        if(!$tasks->isEmpty()){
            $taskInDb = Task::all();

            return $tasks->reject(fn($task) => $task["status"] !== "submitted")->map(function($task) use ($taskInDb){

                $tasks = $taskInDb->firstWhere("id", $task["id"]);

                if(is_null($tasks)){
                    return [];
                }

                return [
                    "id" => $task["id"],
                    "title" => $task["title"],
                    "status" => $task["status"],
                    "description" => $task["description"],
                    "task_deadline_date" => formatDate($tasks->task_deadline_date),
                    "task_deadline_time" => formatTime($tasks->task_deadline_time),
                    "date_submitted" => formatDate($task["date_submitted"]),
                    "linkToResource" => $task["linkToResource"]
                ];
            });

        }

        return $tasks;
    }

    public function pendingTasks(): array|\Illuminate\Support\Collection
    {
        $submittedTasks = collect(json_decode($this->submissions->tasks, true));
        $lessons = $this->lessons();

        $lessons->load('tasks');

        if(!$submittedTasks->isEmpty()){
            $tasks = collect($lessons)->map(fn($lesson)=> $lesson->tasks)->flatten();

            $pending = $tasks->reject(fn($task) => $submittedTasks->firstWhere('id', $task->id))->filter(fn($task) => $task->status !== 'expired')->flatten();

            return $pending->map(fn($task) => new TaskResource($task));
        }

        if(!collect($this->lessons())->isEmpty()){
            return collect($lessons)->map(fn($lesson) => collect($lesson->tasks)->map(fn($task) => new TaskResource($task)))->flatten();
        }

        return [];
    }

    public function expiredTasks(): \Illuminate\Support\Collection
    {
       $lessons = $this->lessons();

       $lessons->load('tasks');

       return collect($lessons)->map(fn($lesson) => collect($lesson->tasks)->filter(fn($task) => $task->status == 'expired')->filter())->flatten();

    }

    public function lessons()
    {
        return $this->course->lessons->orderByAsc('created_at');
    }

    public function track(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Track::class, Course::class);
    }

    public function point(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Point::class);
    }

    public function curriculum(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Curriculum::class, 'plannable');
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Report::class, 'reporter');
    }

    public function progress(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Progress::class);
    }

    public function allowedDevices(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(AllowedDevices::class, "loggable");
    }
}
