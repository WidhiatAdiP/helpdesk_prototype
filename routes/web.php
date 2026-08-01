<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Models\Ticket;

use App\Http\Controllers\UserController;

use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ActivityLogController;


Route::get('/', function () {
    $recentTickets = \App\Models\Ticket::latest()
        ->take(4)
        ->select('id', 'title', 'status', 'priority')
        ->get();

    $totalTickets = \App\Models\Ticket::count();
    $resolvedToday = \App\Models\Ticket::whereDate('updated_at', today())
        ->where('status', 'resolved')
        ->count();
    $resolvedTotal = \App\Models\Ticket::where('status', 'resolved')->count();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'recentTickets' => $recentTickets,
        'stats' => [
            'total' => $totalTickets,
            'resolvedToday' => $resolvedToday,
            'resolutionRate' => $totalTickets > 0
                ? round(($resolvedTotal / $totalTickets) * 100)
                : 0,
        ],
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {


    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');


    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');


    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Tickets
    |--------------------------------------------------------------------------
    */

    Route::get('/tickets', [
        TicketController::class,
        'index'
    ])->name('tickets.index');


    Route::get('/tickets/create', [
        TicketController::class,
        'create'
    ])->name('tickets.create');


    Route::post('/tickets', [
        TicketController::class,
        'store'
    ])->name('tickets.store');

    Route::get('/tickets/{ticket}', [
        TicketController::class,
        'show'
    ])->name('tickets.show');

    Route::post(
        '/tickets/{ticket}/comments',
        [
            TicketController::class,
            'storeComment'
        ]
    )->name('tickets.comments.store');

    Route::patch(
        '/tickets/{ticket}/status',
        [
            TicketController::class,
            'updateStatus'
        ]
    )->name('tickets.status.update');

    Route::patch(
        '/tickets/{ticket}/assign',
        [
            TicketController::class,
            'assign'
        ]
    )->name('tickets.assign');

    Route::get('/reports', [TicketController::class, 'report'])
        ->name('reports.index');

    Route::get('/reports/overview', [TicketController::class, 'reportOverview'])
        ->name('reports.overview');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [
        UserController::class,
        'index',
    ])->name('users.index');

    Route::get('/users/create', [
        UserController::class,
        'create',
    ])->name('users.create');

    Route::post('/users', [
        UserController::class,
        'store',
    ])->name('users.store');

    Route::get(
        '/users/{user}/edit',
        [
            UserController::class,
            'edit'
        ]
    )->name('users.edit');

    Route::patch(
        '/users/{user}',
        [
            UserController::class,
            'update'
        ]
    )->name('users.update');

    Route::get(
        '/users/{user}/password',
        [
            UserController::class,
            'editPassword'
        ]
    )->name('users.password.edit');

    Route::patch(
        '/users/{user}/password',
        [
            UserController::class,
            'updatePassword'
        ]
    )->name('users.password.update');

    Route::delete(
        '/users/{user}',
        [
            UserController::class,
            'destroy'
        ]
    )->name('users.destroy');

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */

    Route::get('/logs/login', [

        LoginLogController::class,

        'index',

    ])->name('logs.login');

    Route::get('/logs', [

        LogController::class,

        'index'

    ])->name('logs.index');

    /*
    |--------------------------------------------------------------------------
    | Activity Logs
    |--------------------------------------------------------------------------
    */

    Route::get('/logs/activity', [

        ActivityLogController::class,

        'index'

    ])->name('logs.activity');
});

require __DIR__.'/auth.php';