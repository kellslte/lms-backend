<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgebaseResource;

class KnowledgebaseController extends Controller
{
    public function __invoke(){
        $user = getAuthenticatedUser();

        switch ($user->email) {
            case strpos($user->email, '.admin'):
                $tag = "admins";
                break;
            
            case strpos($user->email, '.facilitator'):
                $tag = "facilitators";
                break;
            
            case strpos($user->email, '.mentor'):
                $tag = "mentors";
                break;
            
            default:
                $tag = "students";
                break;
        }

        $resources = KnowledgebaseResource::whereTag($tag)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'resources' => $resources
            ]
        ], 200);
    }
}
