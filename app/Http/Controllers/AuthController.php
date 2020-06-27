<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->view_user =[
                'href'=>'api/v1/user/'.$user->id,
                'method' => 'GET'
            ];
        }
        $response = [
            'msg'=>'List of all Users',
            'users'=> $users
        ];
        return response()
            ->json($response, 200);
    }
    public function show($id)
    {
        $user = User::with('properties')
            ->where('id', $id)->firstOrFail();

        $user->view_users = [
        'href'=>'api/v1/user/',
        'method' => 'GET'
        ];

        $response = [
        'msg'=>'User information',
        'user'=> $user
        ];
        return response()
        ->json($response, 200);
    }
    public function store(Request $request)
    {
        $this->validate(
            $request, [
            'name'=>'required',
            'type'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:5'
            ]
        );

        $name = $request->input('name');
        $type = $request->input('type');
        $email = $request->input('email');
        $password = $request->input('password');


        $user = new User(
            [
            'name' => $name,
            'type' => $type,
            'email' => $email,
            'password'=> bcrypt($password)
            ]
        );

        if ($user->save()) {
            $user->signin = [
                'href' => 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            $response = [
                'msg'=>'User created',
                'user'=>$user
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg'=>'An error occured',
        ];

        return response()->json($response, 404);
    }

    public function signin(Request $request)
    {
        $this->validate(
            $request, [
            'email'=>'required|email',
            'password'=>'required'
            ]
        );

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['msg' => 'Invalid credentials'], 401);
        }
        $user=auth()->user();
        $user = User::with('properties')
            ->where('id', $user->id)->firstOrFail();
        $response = [
            'token' => $token,
            'user' => $user,
        ];
        //return $this->respondWithToken($token);
        return response()->json($response, 200);
    }
}
