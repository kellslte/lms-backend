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
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Facilitator extends Authenticatable
{
    use HasFactory, Notifiable, HasUuid,HasApiTokens;

    protected $table = 'facilitators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'recovery_email'
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
        'id'
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

        Mail::to($this->recovery_email)->queue(new SendMagicLinkToUser($data['token'], $data['expires_at'],  $this));
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url =
        config('app.front.url') . '/forgotpassword/resetpassword?token=' . $token.'&email='.$this->email;

        Mail::to($this->recovery_email)->queue(new SendPasswordResetMail($url));
    }

    public function settings()
    {
        return $this->morphOne(Setting::class, 'changeable');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons(){
        return $this->course->lessons;
    }

    public function schedule()
    {
        return $this->morphOne(Schedule::class, 'schedulable');
    }

    public function socials(){
        return $this->morphOne(Social::class, 'sociable');
    }

    public function routeNotificationForMail()
    {
        return $this->recovery_email;
    }
}