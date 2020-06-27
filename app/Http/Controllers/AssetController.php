<?php

namespace App\Http\Controllers;

use App\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssetController extends Controller
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
        $assets = Asset::all();
        foreach ($assets as $asset) {
            $asset->view_asset =[
                'href'=>'api/v1/asset/'.$asset->id,
                'method' => 'GET'
            ];
        }
        $response = [
            'msg'=>'List of all assets',
            'assets'=> $assets
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
            'description'=>'required',
            'property_id'=>'required',
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
        $description = $request->input('description');
        $property_id = $request->input('property_id');
        $user_id = $user->id;

        $asset = new asset(
            [
            'name'=>$name,
            'description'=>$description,
            ]
        );

        if ($asset->save()) {
            $asset->properties()->attach($property_id);
            $asset->view_asset = [
                'href'=>'api/v1/asset/'.$asset->id,
                'method' => 'GET'
            ];
            $message = [
                'msg'=>'Asset created',
                'asset'=> $asset
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

        $asset = asset::with('properties')
            ->where('id', $id)->firstOrFail();

            $asset->view_assets = [
                'href'=>'api/v1/asset/',
                'method' => 'GET'
            ];

            $response = [
            'msg'=>'asset information',
            'asset'=> $asset
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

        $asset = [
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
            'view_asset'=>[
                'href'=>'api/v1/asset/1',
                'method' => 'GET'
            ]
        ];
        $asset = asset::with('users')->findOrFail($id);
        if (!$asset->users()->where('users.id', $user_id)->first()) {
            return response()->json(['msg'=>'user not assigned to asset, update not successful'], 401);
        }
        $asset->name = $name;
        $asset->type = $type;
        $asset->homes = $homes;
        $asset->units = $units;
        $asset->spaces = $spaces;
        $asset->description = $description;
        $asset->state = $state;
        $asset->lga = $lga;
        $asset->name = $town;
        $asset->address = $address;
        $asset->latitude = $latitude;
        $asset->longitude = $longitude;

        if (!$asset->update()) {
            return response()->json(['msg'=>'Error during updating'], 404);
        }

        $asset->view_asset = [
            'href'=> 'api/v1/asset'.$asset->id,
            'method'=>'GET'
        ];

        $response = [
            'msg'=>'asset updated',
            'asset'=> $asset
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

        $asset = asset::findOrFail($id);
        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'User not found'
                ], 404
            );
        }
        if (!$asset->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg'=>'user not assigned to asset, delete operation not successful'], 401);
        }
        $users = $asset->users;
        $asset->users()->detach();
        if (!$asset->delete()) {
            foreach ($users as $user) {
                $asset->users()->attach($user);
            }
            return response()->json(['msg'=>'deletion failed'], 404);
        }

        $response = [
            'msg'=>'asset deleted',
            'create'=>[
                'href'=> 'api/v1/asset',
                'method'=> 'POST',
                'params'=> 'name, type, homes, units, spaces, description, state, lga, town, address, latitude, longitude'
            ]
        ];

        return response()
            ->json($response, 200);
    }
}
