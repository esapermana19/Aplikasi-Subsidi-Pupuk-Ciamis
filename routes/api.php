<?php

use App\Http\Controllers\TransaksiController;

Route::post('/midtrans-callback', [TransaksiController::class, 'notificationHandler']);