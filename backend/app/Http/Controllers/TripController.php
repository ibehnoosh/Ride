<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

         return $request->user()->trip()->create($request->only([
            'origin',
            'destination',
            'destination_name',
        ]));
    }
    public function show(Request $request, Trip $trip)
    {
        //is trip associated with the user
        if($trip->user->id == $request->user()->id){
            return $trip;
        }
        if($trip->driver && $request->user()->driver) {
            if ($trip->driver->id == $request->user()->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message' => 'Cannot find this trip'],404);
    }
    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);
        // Driver accept a trip
        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location,

        ]);

        $trip->load('driver.user');

        return $trip;
    }
    public function start(Request $request, Trip $trip)
    {
        //Driver started taking a passenger to their destination
        $trip->update([
            'is_started' => true

        ]);

        $trip->load('driver.user');

        return $trip;
    }
    public function end(Request $request, Trip $trip)
    {
        //Driver has ended a trip
        $trip->update([
            'is_complete' => true

        ]);

        $trip->load('driver.user');

        return $trip;
    }
    public function location(Request $request, Trip $trip)
    {
        //update the driver's current location

        $request->validate([
            'driver_location' => 'required'
        ]);
        $trip->update([
            'driver_location' => $request->driver_location

        ]);

        $trip->load('driver.user');

        return $trip;
    }
}
