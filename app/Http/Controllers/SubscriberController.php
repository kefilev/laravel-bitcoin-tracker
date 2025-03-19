<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\UnsubscribeRequest;
use App\Models\Subscriber;
use App\Notifications\NewSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{

    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        try {
            // Save to DB
            $subscriber = Subscriber::create($request->only(['email', 'percent', 'period']));

            // Schedule a welcome notification email to the subscriber
            //TODO - send email confirmation link in the message
            $subscriber->notify(new NewSubscriber($subscriber->email));

            // Return standardized success response
            return response()->json([
                'success' => true,
                'message' => "Successful Subscription",
                'data' => $subscriber
            ], 201); // 201 Created
        } catch (\Exception $e) {
            Log::error("Subscription Error: " . $e->getMessage());

            // Return standardized error response
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed. Please try again later.'
            ], 500);
        }
    }

    public function unsubscribe(UnsubscribeRequest $request): JsonResponse
    {
        try {
            // Delete subscriber from DB
            // TODO - include encrypted code in the request (see Laravel signed urls)
            $deleted = Subscriber::where('email', $request->input('email'))->delete();

            return response()->json([
                'success' => true,
                'message' => "Unsubscribed successfully"
            ], 200);
        } catch (\Exception $e) {
            Log::error("Unsubscribe Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unsubscription failed. Please try again later.'
            ], 500);
        }
    }
}
