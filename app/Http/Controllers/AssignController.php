<?php

namespace App\Http\Controllers;

use App\Property;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssignController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
            'property_id'=>'required',
            'user_id'=>'required',
            ]
        );

        $property_id = $request->input('property_id');
        $user_id = $request->input('user_id');
        $abode = $request->input('abode');

        $property = Property::findOrFail($property_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User is already assigned to the property',
            'property' => $property,
            'user' => $user,
            'abode'=>$abode,
            'unassign' => [
                'href' =>'api/v1/property/assign/'.$property->id,
                'method'=>'DELETE'
            ]
        ];

        if ($property->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        }

        $user->properties()->attach($property, ['abode'=>$abode]);

        $response = [
            'msg' => 'User assigned to property',
            'property' => $property,
            'user' => $user,
            'abode'=>$abode,
            'unassign' => [
                'href' =>'api/v1/property/assign/'.$property->id,
                'method'=>'DELETE'
            ]
        ];

        return response()
        ->json($response, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'User not found'
                ], 404
            );
        }
        if (!$property->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg'=>'user not assigned to property, delete operation not successful'], 401);
        }

        $property->users()->detach($user->id);

        $response = [
            'msg' => 'User unassigned from property',
            'property' => $property,
            'user' => $user,
            'abode'=>'Abode',
            'assign' => [
                'href' =>'api/v1/property/assign',
                'method'=>'POST',
                'params'=>'user_id, meeting_id, abode'
            ]
        ];

        return response()
        ->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function unassign(Request $request)
    {
        $this->validate(
            $request, [
            'property_id'=>'required',
            'user_id'=>'required',
            ]
        );

        $property_id = $request->input('property_id');
        $user_id = $request->input('user_id');

        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'User not found'
                ], 404
            );
        }

        $property = Property::findOrFail($property_id);
        $user = User::findOrFail($user_id);

        if (!$property->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg'=>'user not assigned to property, delete operation not successful'], 401);
        }

        $property->users()->detach($user->id);

        $response = [
            'msg' => 'User unassigned from property',
            'property' => $property,
            'user' => $user,
            'abode'=>'Abode',
            'assign' => [
                'href' =>'api/v1/property/assign',
                'method'=>'POST',
                'params'=>'user_id, meeting_id, abode'
            ]
        ];

        return response()
        ->json($response, 200);
    }
}
