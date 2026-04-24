<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AutreLinkController;
use App\Http\Controllers\BonLivraisonController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\InfractionController;
use App\Http\Controllers\LigneCommandeController;
use App\Http\Controllers\LocationSocieteController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrimeDeplacementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SemiRemorqueController;
use App\Http\Controllers\TarifClientController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\VilleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/users/create', [RegisteredUserController::class, 'create'])
        ->name('showCreateUser')
        ->middleware('role:super_admin');
    Route::post('/users/register', [RegisteredUserController::class, 'store'])
        ->name('createUser')
        ->middleware('role:super_admin');

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::resource('villes', VilleController::class);
    Route::resource('articles', ArticleController::class)->except(['show']);
    Route::resource('chauffeurs', ChauffeurController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('location_societes', LocationSocieteController::class);
    Route::resource('vehicules', VehiculeController::class);
    Route::resource('semi_remorques', SemiRemorqueController::class);
    Route::resource('trajets', TrajetController::class);
    Route::resource('absences', AbsenceController::class);
    Route::resource('tarif_clients', TarifClientController::class);
    Route::resource('prime_deplacements', PrimeDeplacementController::class);
    Route::resource('commandes', CommandeController::class);
    Route::resource('ligne_commandes', LigneCommandeController::class);
    Route::resource('bon_livraisons', BonLivraisonController::class);
    Route::get('/bon-livraisons/{bonLivraison}/download', [BonLivraisonController::class, 'downloadPdf'])
        ->name('bon_livraisons.pdf');

    Route::resource('factures', FactureController::class);
    Route::get('/factures/{facture}/download', [FactureController::class, 'downloadPdf'])
        ->name('factures.pdf');
    Route::resource('infractions', InfractionController::class);
    Route::resource('maintenances', MaintenanceController::class);


    Route::get('autresLink', [AutreLinkController::class, 'index'])->name('autresLink');
});

require __DIR__ . '/auth.php';
