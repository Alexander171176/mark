<?php
// Работа с командами (Teams)

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\CurrentTeamController;
use Laravel\Jetstream\Http\Controllers\Inertia\TeamController;
use Laravel\Jetstream\Http\Controllers\Inertia\TeamMemberController;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;

// --- Teams ---
// Форма создания команды
Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
// Сохранение новой команды
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
// Показ страницы команды
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
// Обновление названия команды
Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
// Удаление команды
Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

// Участники команды
Route::post('/teams/{team}/members', [TeamMemberController::class, 'store'])->name('team-members.store');
Route::put('/teams/{team}/members/{user}', [TeamMemberController::class, 'update'])->name('team-members.update');
Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('team-members.destroy');

// Приглашения в команду
Route::post('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])->name('team-invitations.accept');
Route::delete('/team-invitations/{invitation}', [TeamInvitationController::class, 'destroy'])->name('team-invitations.destroy');

// Переключение текущей команды
Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
