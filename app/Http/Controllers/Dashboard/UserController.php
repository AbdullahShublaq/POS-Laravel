<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:read_users'])->only('index');
        $this->middleware(['permission:create_users'])->only(['create', 'store']);
        $this->middleware(['permission:update_users'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_users'])->only('destroy');
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        abort(403);
        $users = User::whereRoleIs('admin')->where(function ($query) use ($request) {

            $query->when($request->search, function ($q) use ($request) {
                return $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate(10);

        return view('dashboard.users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|unique:users',
            'image'         => 'image',
            'password'      => 'required|confirmed',
            'permissions'   => 'required|min:1'
        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        if ($request->image) {
            Image::make($request->image)->resize(300, NULL, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        return view('dashboard.users.edit')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'image'         => 'image',
            'password'      => 'confirmed',
            'permissions'   => 'required|min:1'
        ]);
        $request_data = $request->except(['permissions', 'image', 'password', 'password_confirmation']);
        if($request->password){
            $request_data['password'] = bcrypt($request->password);
        }
        if ($request->image) {
            if ($user->image != 'default.png') {
                Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
            }

            Image::make($request->image)->resize(300, NULL, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        $user->update($request_data);
        $user->syncPermissions($request->permissions);



        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        //
        if ($user->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
        }
        $user->delete();

        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');
    }
}
