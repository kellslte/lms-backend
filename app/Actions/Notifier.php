<?php
namespace App\Actions;

use Spatie\SlackAlerts\Facades\SlackAlert;

class Notifier {
    public static function notify(String $track, String $message){
        switch ($track) {
            case 'Backend Engineering':
                SlackAlert::to("web-backend")->message($message);
            break;

            case 'Product Design':
                SlackAlert::to("default")->message($message);
            break;

            case 'Frontend Engineering':
                SlackAlert::to("web-frontend")->message($message);
            break;

            case 'Mobile Application Development':
                SlackAlert::to("default")->message($message);
            break;

            case 'Data':
                SlackAlert::to("default")->message($message);
            break;

            case 'Product Management':
                SlackAlert::to("product-mgt")->message($message);
            break;

            case 'Cloud Engineering':
                SlackAlert::to("default")->message($message);
            break;

            default:
                SlackAlert::to("tooling-docs-technical-writing")->message($message);
            break;
        }
    }
}