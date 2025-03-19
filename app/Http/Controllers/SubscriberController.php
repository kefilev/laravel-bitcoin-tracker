<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\UnsubscribeRequest;
use App\Http\Resources\SubscriberResource;
use App\Http\Responses\BaseApiResponse;
use App\Models\Subscriber;
use App\Notifications\NewSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{

    public function subscribe(SubscribeRequest $request): SubscriberResource | JsonResponse
    {
        try {
            // Save to DB
            $subscriber = Subscriber::create($request->only(['email', 'percent', 'period']));

            // Schedule a welcome notification email to the subscriber
            //TODO - send email confirmation link in the message
            $subscriber->notify(new NewSubscriber($subscriber->email));

            return new SubscriberResource($subscriber);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return BaseApiResponse::error();
        }
    }

    public function unsubscribe(UnsubscribeRequest $request): JsonResponse
    {
        try {
            // Delete subscriber from DB
            // TODO - include encrypted code in the request (see Laravel signed urls)
            Subscriber::where('email', $request->input('email'))->delete();

            return BaseApiResponse::success();
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return BaseApiResponse::error();
        }
    }
}
