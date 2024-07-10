<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
class EventController extends Controller
{
    public function index()
    {
        return Event::all();
    }
    public function getUserListings ()
    {
       
        $user =Auth::user()->id; // Retrieve authenticated user
        $listings = Event::where('user_id', $user)->get(); // Fetch user's events

   

        return response()->json([
         
            'listings' => $listings,
        ]);
    }

    public function show($id)
    {
        return Event::find($id);
    }

    public function store(Request $request)
    {
     
        $request->validate([
            'title' => 'required|string|max:255',
            'event_type' => 'required|in:online,physical,hybrid',
            'country' => 'required|string',
            'venue' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'category' => 'required|string',
            'website_link' => 'required|url',
            'description' => 'required|string',
            'video_link' => 'nullable|url',
            // 'featured_photo' => 'required|image|max:5000'
        ]);
    
        // Handle the image upload
        $imagePath = null;
        if ($request->filled('featured_photo')) {
            $base64Image = $request->input('featured_photo');
            $extension = explode('/', mime_content_type($base64Image))[1]; // Extract extension from base64 string
    
            // Generate a unique filename
            $filename = time() . '.' . $extension;
            $imagePath = 'images/events/' . $filename;
    
            // Decode and save the base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            file_put_contents(public_path($imagePath), $imageData);
        }
    
        // Create the event
        $event = Event::create([
            'user_id' =>$request->user_id,
            'title' => $request->title,
            'event_type' => $request->event_type,
            'country' => $request->country,
            'venue' => $request->venue,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'category' => $request->category,
            'website_link' => $request->website_link,
            'description' => $request->description,
            'video_link' => $request->video_link,
            'featured_photo' => $imagePath
        ]);
    
        return response()->json($event, 201);
    }
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());
        return response()->json($event, 200);
    }

    public function destroy($id)
    {
   ; 
       
        Event::where('user_id', Auth::user()->id)->where('id',$id)->delete();
        return response()->json(null, 204);
    }
}
