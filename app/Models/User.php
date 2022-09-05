<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasUuid;
use App\Mail\SendMagicLinkToUser;
use Laravel\Sanctum\HasApiTokens;
use App\Services\MagicLinkService;
use App\Mail\SendPasswordResetMail;
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

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = config('app.front.url') . '/auth/password/students/reset?token=' . $token.'&email='.$this->email;

        Mail::to($this->email)->queue(new SendPasswordResetMail($url));
    }

    public function submissions(){
        return $this->morphMany(Submission::class, 'taskable');
    }

    public function schedule(){
        return $this->morphOne(Schedule::class, 'schedulable');
    }

    public function attendance(){
        return $this->morphOne(Attendance::class, 'attender');
    }

    public function settings(){
        return $this->morphOne(Setting::class, 'changeable');
    }

    public function completedTasks(){
        $tasks  = $this->submissions()->whereStatus('submitted')->get();

        return collect($tasks)->map(function($task){
            return Task::find($task->submittable_id);
        });
    }

    public function pendingTasks(){
        $tasks = collect($this->submissions)->map(function($submission){
            return Task::find($submission->submittable_id);
        });

        $allTasks = collect($this->course->lessons)->map(function ($lesson) {
            return $lesson->task;
        })->filter(function ($lesson) {
            return $lesson->status == 'pending' || $lesson->status == 'graded';
        })->flatten();

        return collect($allTasks)->map(function($task) use ($tasks){
            return !$tasks->contains($task) ? $task: null;
        })->filter()->flatten();
    }

    public function expiredTasks(){
       $lessons = $this->course->lessons;
        
       return collect($lessons)->map(function($lesson){
            return $lesson->task;
       })->filter(function($lesson){
        return $lesson->status == 'expired';
       })->flatten();  
    }

    public function lessons()
    {
        return $this->course->lessons;
    }

    public function track(){
        return $this->hasOneThrough(Track::class, Course::class);
    }

    public function point(){
        return $this->hasOne(Point::class);
    }

    public function mentor(){
        return $this->morphOne(Mentees::class, 'mentorable');
    }

    public function curriculum(){
        return $this->morphOne(Curriculum::class, 'plannable');
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reporter');
    }
}
