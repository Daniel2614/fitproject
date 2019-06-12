<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileFormRequest;

class UserProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('admin.profile', compact('user'));
    }

    public function update(UpdateUserProfileFormRequest $request)
    {
        auth()->user()->update($request->userParams());

        return redirect()->route('admin.profile.edit')->with(['message' => 'Perfil Actualizado con éxito', 'alert-type' => 'success']);
    }
}
