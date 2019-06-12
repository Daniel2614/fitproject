<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateUserFormRequest;
use App\Http\Requests\UpdateUserFormRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereRole('admin')
                     ->whereNotIn('id', [auth()->user()->id])
                     ->orderByDesc('id')
                     ->paginate();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);

        return view('admin.users.edit', compact('user'));
    }

    public function store(CreateUserFormRequest $request)
    {
        User::createWithActivationToken($request->userParams());

        return redirect()->route('admin.users.index')->with(['message' => 'Usuario creado con éxito', 'alert-type' => 'success']);
    }

    public function update(UpdateUserFormRequest $request, $userId)
    {
        User::findOrFail($userId)->update($request->userParams());

        return redirect()->route('admin.users.index')->with(['message' => 'Usuario actualizado con éxito', 'alert-type' => 'success']);
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        if (! $user->avatar === 'avatars/default-avatar.jpg') {
            Storage::delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with(['message' => 'Usuario Eliminado con éxito', 'alert-type' => 'success']);
    }
}
