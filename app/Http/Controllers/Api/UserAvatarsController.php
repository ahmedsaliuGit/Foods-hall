<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserAvatarsController extends Controller
{
    public function store()
    {
        $this->validate(request(), [
            'avatar' => ['required', 'image']
        ]);

        $fileUrl = request()->file('avatar')->store('avatars', 'public');

        auth()->user()->update([
            'avatar_path' => Storage::url($fileUrl)
        ]);

        return response([], 204);
    }
}
