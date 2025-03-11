<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\UnsubscribeRequest;
use App\Models\Subscriber;
use App\Notifications\NewSubscriber;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    public function subscribe(SubscribeRequest $request) {
        try {
            //Save to DB
            $subscriber = new Subscriber();
            $subscriber->email = $request->input('email');
            $subscriber->percent = $request->input('percent');
            $subscriber->period = $request->input('period');
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
        return response()->json(['success' => "You have successfully subscribed your email - {$request->input('email')} for the bitcoin tracker"], 200);
    }

    
    public function unsubscribe(UnsubscribeRequest $request) {
        try {
            //Delete from DB the unsubscribed user
            Subscriber::where('email', $request->input('email'))->delete();

            //Schedule a notification to let the subscriber know that he is no longer a subscriber
            //TODO
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // return error
            return response()->json(['error' => 'Server Error'], 500);
        }

        //Return OK
        return response()->json(['success' => "You have successfully unsubscribed your email - {$request->input('email')} from the bitcoin tracker"], 200);
    }
}
