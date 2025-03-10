<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Notifications\NewSubscriber;
use Illuminate\Http\Request;

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

            //Save to DB
            $subscriber = new Subscriber();
            $subscriber->email = $request->query('email');
            $subscriber->percent = $request->query('percent');
            $subscriber->period = $request->query('period');
            $saved = $subscriber->save();

            //Schedule a notification email to let the subscriber know that he has subscribed
            //TODO
            if ($saved) {
                $subscriber->notify(new NewSubscriber($subscriber->email));
            }
        } catch (\Exception $e) {
            // return error
            return response()->json(['error' => $e->getMessage()], 400);
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

            //forceDelete from DB the unsubscribed users
            Subscriber::where('email', $validated['email'])->delete();

            //Schedule a notification to let the subscriber know that he is no longer a subscriber
            //TODO
        } catch (\Exception $e) {
            // return error
            return response()->json(['error' => $e->getMessage()], 400);
        }

        //Return OK
        return response()->json(['success' => "You have successfully unsubscribed your email - {$validated['email']} from the bitcoin tracker"], 200);
    }
}
