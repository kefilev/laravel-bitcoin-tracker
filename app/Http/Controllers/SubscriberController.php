<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Notifications\NewSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    public function subscribe(Request $request) {
        try {
            //Validate params
            $validated = $request->validate([
                'email' => 'bail|required||email:rfc,dns|unique:subscribers',
                'percent' => 'bail|required|numeric',
                'period' => 'bail|required|in:1,6,24'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // return error
            return response()->json(['error' => $e->getMessage()], 400);
        }

        try {
            //Save to DB
            $subscriber = new Subscriber();
            $subscriber->email = $request->query('email');
            $subscriber->percent = $request->query('percent');
            $subscriber->period = $request->query('period');
            $saved = $subscriber->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // return error
            return response()->json(['error' => 'Server Error'], 500);
        }

        //Schedule a notification email to let the subscriber know that he has subscribed
        if ($saved) {
            $subscriber->notify(new NewSubscriber($subscriber->email));
        }

        //Return OK
        return response()->json(['success' => "You have successfully subscribed your email - {$validated['email']} for the bitcoin tracker"], 200);
    }

    
    public function unsubscribe(Request $request) {
        try {
            //Validate params
            $validated = $request->validate([
                'email' => 'bail|required||email:rfc,dns|exists:subscribers'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // return error
            return response()->json(['error' => $e->getMessage()], 400);
        }

        try {
            //Delete from DB the unsubscribed user
            Subscriber::where('email', $validated['email'])->delete();

            //Schedule a notification to let the subscriber know that he is no longer a subscriber
            //TODO
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // return error
            return response()->json(['error' => 'Server Error'], 500);
        }

        //Return OK
        return response()->json(['success' => "You have successfully unsubscribed your email - {$validated['email']} from the bitcoin tracker"], 200);
    }
}
