<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __invoke(Request $request): UserResource
    {
        return UserResource::make($request->user()->load('assets'));
    }
}
