<?php

// routes/api.php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/reports/{type}', [ReportController::class, 'download&']);