<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $rooms = Room::all();

        return view('room.index',compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('room.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'tax' => 'required',
            'dinner_fee' => 'required',
            'breakfast_fee' => 'required',
            'capacity' => 'required',
            'bed_size' => 'required',
        ]);

        $room = new Room();
        $room->name = $request->input('name');
        $room->detail = $request->input('detail');
        $room->price = $request->input('price');
        $room->tax = $request->input('tax');
        $room->dinner_fee = $request->input('dinner_fee');
        $room->breakfast_fee = $request->input('breakfast_fee');
        $room->capacity= $request->input('capacity');
        $room->bed_size = $request->input('bed_size');
        $room->smorking = $request->input('smorking');
        $room->facility = $request->input('facility');
        $room->amenities = $request->input('amenities');
        $room->status = $request->input('status');
        
        if ($request->hasFile('img')) {
            $image_path = $request->file('img')->store('public/room_img/');
            $room->img = basename($image_path);
        }
        
        
        $room->save();

        return redirect()->route('room.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {

        return view('room.show',compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        
        return view('room.edit',compact('room'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'tax' => 'required',
            'dinner_fee' => 'required',
            'breakfast_fee' => 'required',
            'capacity' => 'required',
            'bed_size' => 'required',

        ]);

        $room->name = $request->input('name');
        $room->detail = $request->input('detail');
        $room->price = $request->input('price');
        $room->tax = $request->input('tax');
        $room->dinner_fee = $request->input('dinner_fee');
        $room->breakfast_fee = $request->input('breakfast_fee');
        $room->capacity= $request->input('capacity');
        $room->bed_size = $request->input('bed_size');
        $room->smorking = $request->input('smorking');
        $room->facility = $request->input('facility');
        $room->amenities = $request->input('amenities');
        $room->status = $request->input('status');
        
        if ($request->hasFile('img')) {
            $image_path = $request->file('img')->store('public/room_img/');
            $room->img = basename($image_path);
        }
        
        
        $room->save();

        return redirect()->route('room.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('room.index');
    }
}
