<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Mission\Broadcast;
use App\Mission\Settlement;
use App\Mission\DailyCut;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('broadcast', function () {
    $broadcast = new Broadcast();
    $broadcast->apprise();
})->purpose('Notice of payment result');

Artisan::command('supplement', function () {
    $broadcast = new Broadcast();
    $broadcast->supplement();
})->purpose('Notice of payment result');

Artisan::command('error_report', function () {
    $broadcast = new Broadcast();
    $broadcast->bot();
})->purpose('Notice of error_report');
//
Artisan::command('debt_report', function () {
    $broadcast = new Broadcast();
    $broadcast->debt();
})->purpose('Notice of error_report');
//
Artisan::command('bot:refresh', function () {
    $broadcast = new Broadcast();
    $broadcast->BotRefresh();
})->purpose('Notice of error_report');

Artisan::command('settlement {chat_id}', function ($chat_id)  {
    $broadcast = new Settlement();
    $broadcast->index($chat_id);
})->purpose('Notice of error_report');


Artisan::command('issued {chat_id}', function ($chat_id)  {
    $broadcast = new Settlement();
    $broadcast->issued($chat_id);
})->purpose('Notice of error_report');



Artisan::command('chat:refresh', function ()  {
    $broadcast = new Broadcast();
    $broadcast->chatRefresh();
})->purpose('Notice of error_report');

Artisan::command('redis:refresh', function ()  {
    $broadcast = new Broadcast();
    $broadcast->redisRefresh();
})->purpose('Notice of error_report');

Artisan::command('channel:refresh', function ()  {
    $broadcast = new Broadcast();
    $broadcast->channelRefresh();
})->purpose('Notice of error_report');

Artisan::command('daily:message', function ()  {
    $broadcast = new DailyCut();
    $broadcast->message();
})->purpose('Notice of error_report');

Artisan::command('daily:openchannel', function ()  {
    $broadcast = new DailyCut();
    $broadcast->openChannel();
})->purpose('Notice of error_report');
