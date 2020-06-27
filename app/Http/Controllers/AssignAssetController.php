<?php

namespace App\Http\Controllers;

use App\Property;
use App\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssignAssetController extends Controller
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
            'asset_id'=>'required',
            ]
        );

        $property_id = $request->input('property_id');
        $asset_id = $request->input('asset_id');
        $abode = $request->input('abode');

        $property = Property::findOrFail($property_id);
        $asset = Asset::findOrFail($asset_id);

        $message = [
            'msg' => 'Asset is already assigned to the property',
            'property' => $property,
            'asset' => $asset,
            'abode'=>$abode,
            'unassign' => [
                'href' =>'api/v1/property/asset/'.$property->id,
                'method'=>'DELETE'
            ]
        ];

        if ($property->assets()->where('assets.id', $asset->id)->first()) {
            return response()->json($message, 404);
        }

        $asset->properties()->attach($property);

        $response = [
            'msg' => 'asset assigned to property',
            'property' => $property,
            'asset' => $asset,
            'abode'=>$abode,
            'unassign' => [
                'href' =>'api/v1/property/asset/'.$property->id,
                'method'=>'DELETE'
            ]
        ];

        return response()
        ->json($response, 201);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function unassign(Request $request)
    {
        $this->validate(
            $request, [
            'property_id'=>'required',
            'asset_id'=>'required',
            ]
        );

        $property_id = $request->input('property_id');
        $asset_id = $request->input('asset_id');
        $abode = $request->input('abode');

        $property = Property::findOrFail($property_id);
        $asset = Asset::findOrFail($asset_id);

        if (! $user = auth()->user()) {
            return response()->json(
                [
                'msg' => 'user not found'
                ], 404
            );
        }
        if (!$asset->properties()->where('properties.id', $property->id)->first()) {
            return response()->json(['msg'=>'asset not assigned to property, delete operation not successful'], 401);
        }

        $asset->properties()->detach($property->id);

        $response = [
            'msg' => 'asset unassigned from property',
            'property' => $property,
            'asset' => $asset,
            'assign' => [
                'href' =>'api/v1/property/asset',
                'method'=>'POST',
                'params'=>'asset_id, property_id'
            ]
        ];

        return response()
        ->json($response, 200);

    }
}
