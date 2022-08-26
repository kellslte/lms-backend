<?php
namespace App\Services;

use Aws\Sns\SnsClient;

class SmsService {
    protected static $client;
  
  private static function initialize(){
    $keys = [
      'credentials' => [
        'key' => config('services.sns.key'),
        'secret' => config('services.sns.secret'),
      ],
      'region' => config('services.sns.region'),
      'version' => 'latest'
    ];

    self::$client = new SnsClient($keys);
  }

  public static function send($message, $receipient, $sender){
    self::initialize();

    $data = [
      'MessageAttributes' => [
        'AWS.SNS.SMS.SMSType' => [
          'DataType' => 'String',
          'StringValue' => 'Transactional'
        ],
        'AWS.SNS.SMS.SenderID' => [
          'DataType' => 'String',
          'StringValue' => $sender
        ]
        ],
        'Message' => $message,
        'PhoneNumber' => $receipient
    ];

    return self::$client->publish($data) ? true : false;
  }
}