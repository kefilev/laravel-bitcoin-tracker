# Laravel email subscription service App for Bitcoin Price Changes

This is a simple Bitcoin Price subscription service built with Laravel 12 that lets users subscribe to the service trough API (using get requests) and receive reports by email every hour, every 6 hours or every 24 hours, depending on their subscription. The app uses bitfinex.com to retrieve Bitcoin Price data but can easily be configured to use any other service. Users should specify a percentage threshold and a time interval on registration. Available time intervals: 1 hour, 6 hours, 24 hours.

Example scenario: If a user sets a 10% threshold for a 6-hour period, they should receive a notification if the price fluctuates by more than 10% within the last 6 hours.

## Requirements

Web `server` like Apache, nginx or XAMPP with `PHP 8.2` and `MySQL`. Also install `composer` and `artisan` (if you install Laravel globally from the Laravel Installer this will include artisan for your system).

## Configuration

Run in console:

`composer install` to install the PHP dependencies in vendor folder

`npm install` to install the JS dependencies in node_modules folder

Configure your .env file. For example:

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=....
MAIL_PASSWORD=....

#### Additional
BITFINEX_API_URL=https://api-pub.bitfinex.com/v2/
```

`php artisan key:generate` to generate encryption key for Laravel

`php artisan migrate` to create the database

## Running the app

Make sure you have your server, php and mysql running (Latest XAMPP for example). Then make sure you see the Laravel 12 homepage on the browser by visiting `http://localhost`.

## Subscription

To subscribe send a POST request to: `/api/subscribe` with the following body: 
email={your@email.com}
percent={int or float}
period={1|6|24}

After you subscribe you should receive an email confirming your subscription and a link to unsubscribe. For more realistic scenario an email confirmation link should be provided and the corresponding logic in the backend should be implemented.

To unsubscribe go to (GET request): `/api/unsubscribe?email=your@email.com`

Using a GET request for the unsubscribe, so it can be accessed from a link. For more realistic scenario an encrypted key for each user must be included in the unsubscribe link. Check Laravel signed urls for more info...

## Tests

Two additional test files are included in the tests/Feature and tests/Unit/Services directories. SubscriptionAPITest.php does HTTP tests for the API subscribe and unsubscribe endpoints using PHPUnit. The BitFinexServiceTest.php file tests the BitfinexService.php functions.

To run the tests execute `php artisan test` in the console.

For tests to pass the `APP_URL` .env variable should point to `http://localhost`. If changing the .env or other configurations make sure to execute `php artisan config:clear` before running tests.

## Database structure

The app uses MySQL database with one additional table for the email subscribers that holds the email, percent and period for each subscriber. When the user unsubscribe himself he gets deleted from the database. 

To run the migrations execute in the console:

`php artisan migrate`

## Sending the welcome emails to subscribers

To send the welcome emails to subscribers we use queue worker. To run the queue:

`php artisan queue:work`

## Sending the Bitcoin Price report emails

We use task scheduling with a queue worker to send the reports. For local development we can run the schedule for sending reports like this:

`php artisan schedule:work`

Also make sure the queue is running:

`php artisan queue:work`

The schedule is defined in the routes/console.php file. To test if the emails are coming you can change `daily` or any other function to `everyMinute`.

The logic for sending the report emails is here: `app\Console\Commands\SendBitcoinInfoNotifications.php`

## Emails

In this app we use Laravel Notifications to send emails. The Notifications are located in App\Notifications folder.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

