<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    echo "<br>";
    $world = \Illuminate\Support\Facades\Artisan::call("generate:world", ['--monsters' => \Illuminate\Support\Facades\Input::get('monsters', 10), '--size' => \Illuminate\Support\Facades\Input::get('size', 'small')]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "<br>World Left<br><br>";
    $world = \Illuminate\Support\Facades\Cache::get('world.left');
    \Illuminate\Support\Facades\Cache::forget('world.left');

    if ($world) {
        echo str_replace(PHP_EOL, "<br>", $world);
    }
});
