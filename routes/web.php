<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('contacts', ContactController::class);
Route::get('contacts/{contact}/merge', [ContactController::class, 'show_merge_form'])->name('contacts.merge');
Route::post('contacts/{contact}/merge', [ContactController::class, 'merge'])->name('contacts.merge.submit');

Route::resource('custom-fields', CustomFieldController::class)->except(['show']);

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/contacts', [ContactController::class, 'api_index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'api_store'])->name('contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'api_update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'api_destroy'])->name('contacts.destroy');
    Route::get('/contacts/search', [ContactController::class, 'api_search'])->name('contacts.search');
});
