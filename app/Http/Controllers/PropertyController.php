<?php

namespace App\Http\Controllers;

use App\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            'auth:api', ['only' =>[
            'update', 'store', 'destroy'
            ]]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::all();
        foreach ($properties as $property) {
            $property->view_property =[
                'href'=>'api/v1/property/'.$property->id,
                'method' => 'GET'
            ];
        }
        $response = [
            'msg'=>'List of all Properties',
            'properties'=> $properties
        ];
        return response()
            ->json($response, 200);
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
            'name'=>'required',
            'type'=>'required',
            'description'=>'required',
            'state'=>'required',
            'lga'=>'required',
            'town'=>'required',
            'address'=>'required',
            'latitude'=>'required',
            'longitude'=>'required'
            ]
        );

        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'User not found'
                ], 404
            );
        }

        $name = $request->input('name');
        $type = $request->input('type');
        $homes = $request->input('homes');
        $units = $request->input('units');
        $spaces = $request->input('spaces');
        $description = $request->input('description');
        $state = $request->input('state');
        $lga = $request->input('lga');
        $town = $request->input('town');
        $address = $request->input('address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $user_id = $user->id;

        $property = new Property(
            [
            'name'=>$name,
            'type'=>$type,
            'homes'=>$homes,
            'units'=>$units,
            'spaces'=>$spaces,
            'description'=>$description,
            'state' => $state,
            'lga' => $lga,
            'town' => $town,
            'address'=>$address,
            'latitude'=>$latitude,
            'longitude'=>$longitude
            ]
        );

        if ($property->save()) {
            $abode = $user ? $user->type:"Manager";
            $property->users()->attach($user_id, ['abode' => $abode]);
            $property->view_property = [
                'href'=>'api/v1/property/'.$property->id,
                'method' => 'GET'
            ];
            $message = [
                'msg'=>'Property created',
                'property'=> $property
            ];

            return response()
                ->json($message, 201);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $property = Property::with('users', 'assets')
            ->where('id', $id)->firstOrFail();

            $property->view_properties = [
                'href'=>'api/v1/property/',
                'method' => 'GET'
            ];

            $response = [
            'msg'=>'Property information',
            'property'=> $property
            ];
            return response()
            ->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(
            $request, [
            'name'=>'required',
            'type'=>'required',
            'description'=>'required',
            'state'=>'required',
            'lga'=>'required',
            'town'=>'required',
            'address'=>'required',
            'latitude'=>'required',
            'longitude'=>'required',
            ]
        );

        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'User not found'
                ], 404
            );
        }

        $name = $request->input('name');
        $type = $request->input('type');
        $homes = $request->input('homes');
        $units = $request->input('units');
        $spaces = $request->input('spaces');
        $description = $request->input('description');
        $state = $request->input('state');
        $lga = $request->input('lga');
        $town = $request->input('town');
        $address = $request->input('address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $user_id = $user->id;

        $property = [
            'name'=>$name,
            'type'=>$type,
            'homes'=>$homes,
            'units'=>$units,
            'spaces'=>$spaces,
            'description'=>$description,
            'state' => $state,
            'lga' => $lga,
            'town' => $town,
            'address'=>$address,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'user_id'=>$user_id,
            'view_property'=>[
                'href'=>'api/v1/property/1',
                'method' => 'GET'
            ]
        ];
        $property = Property::with('users')->findOrFail($id);
        if (!$property->users()->where('users.id', $user_id)->first()) {
            return response()->json(['msg'=>'user not assigned to property, update not successful'], 401);
        }
        $property->name = $name;
        $property->type = $type;
        $property->homes = $homes;
        $property->units = $units;
        $property->spaces = $spaces;
        $property->description = $description;
        $property->state = $state;
        $property->lga = $lga;
        $property->name = $town;
        $property->address = $address;
        $property->latitude = $latitude;
        $property->longitude = $longitude;

        if (!$property->update()) {
            return response()->json(['msg'=>'Error during updating'], 404);
        }

        $property->view_property = [
            'href'=> 'api/v1/property'.$property->id,
            'method'=>'GET'
        ];

        $response = [
            'msg'=>'Property updated',
            'property'=> $property
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
        $users = $property->users;
        $property->users()->detach();
        if (!$property->delete()) {
            foreach ($users as $user) {
                $property->users()->attach($user);
            }
            return response()->json(['msg'=>'deletion failed'], 404);
        }

        $response = [
            'msg'=>'Property deleted',
            'create'=>[
                'href'=> 'api/v1/property',
                'method'=> 'POST',
                'params'=> 'name, type, homes, units, spaces, description, state, lga, town, address, latitude, longitude'
            ]
        ];

        return response()
            ->json($response, 200);
    }
}
