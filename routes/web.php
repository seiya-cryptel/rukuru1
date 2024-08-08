<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\Localization;

use App\Livewire\Employeeworks;

/**
 * Redirect to the default locale
*/
Route::get('/', function(){
    return redirect('/' . app()->getLocale());
 });

/**
 * Set the locale
 */
Route::get('/setlocale/{lang}', function($lang) {
    app()->setLocale($lang);
    session(['localization' => $lang]);
    // return redirect()->back();
    return redirect("/" . $lang);
})->name('localization');

/**
 * Routes that require localization
 */
Route::group(['prefix' => '{locale}', 'middleware' => Localization::class], 
function () {
        Route::view('/', 'welcome');

        Route::view('dashboard', 'dashboard')
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        /**
         * Route for the 顧客マスタ
         */
        Route::view('client', 'client')
            ->middleware(['auth', 'verified'])
            ->name('client');

        /**
         * Route for the 顧客事業所マスタ
         */
        Route::view('clientplace', 'clientplace')
            ->middleware(['auth', 'verified'])
            ->name('clientplace');

        /**
         * Route for the 作業種別マスタ
         */
        Route::view('clientworktype', 'clientworktype')
            ->middleware(['auth', 'verified'])
            ->name('clientworktype');

        /**
         * Route for the 請求単価マスタ
         */
        Route::view('pricetable', 'pricetable')
            ->middleware(['auth', 'verified'])
            ->name('pricetable');

        /**
         * Route for the 手当控除項目マスタ
         */
        Route::view('masterallowdeduct', 'masterallowdeduct')
            ->middleware(['auth', 'verified'])
            ->name('masterallowdeduct');

        /**
         * Route for the 従業員マスタ
         */
        Route::view('employee', 'employee')
            ->middleware(['auth', 'verified'])
            ->name('employee');

        /**
         * Route for the アカウント一覧
         */
        Route::view('user', 'user')
            ->middleware(['auth', 'verified'])
            ->name('user');

        /**
         * Routes for the 勤怠インポート
         */
        Route::view('importkintai', 'importkintai')
            ->middleware(['auth', 'verified'])
            ->name('importkintai');

        /**
         * Routes for the 勤怠エントリー 従業員一覧
         */
        Route::view('workemployee', 'workemployee')
            ->middleware(['auth', 'verified'])
            ->name('workemployee');
    
        /**
        * Routes for the 勤怠エントリー 個人別  
        */
        Route::view('employeework/', 'employeework')
            ->middleware(['auth', 'verified'])
            ->name('employeework');

        /**
         * Routes for the 勤怠エントリー 勤怠締め
         */
        Route::view('closepayroll/', 'closepayroll')
            ->middleware(['auth', 'verified'])
            ->name('closepayroll');

        /**
         * Routes for the 請求　請求書出力
         */
        Route::view('bills', 'bills')
            ->middleware(['auth', 'verified'])
            ->name('bills');

        /**
         * Routes for the 請求　請求明細表示
         */
        Route::view('billdetails', 'billdetails')
            ->middleware(['auth', 'verified'])
            ->name('billdetails');

        /**
         * Routes for the 給与　手当控除入力
         */
        Route::view('deductentry', 'deductentry')
            ->middleware(['auth', 'verified'])
            ->name('deductentry');

        /**
         * Routes for the 給与　従業員手当控除
         */
        Route::view('deductperson', 'deductperson')
            ->middleware(['auth', 'verified'])
            ->name('deductperson');

        /**
         * Routes for the 給与　給与計算
         */
        Route::view('salarycalc', 'salarycalc')
            ->middleware(['auth', 'verified'])
            ->name('salarycalc');
    
        Route::view('profile', 'profile')
            ->middleware(['auth'])
            ->name('profile');
}
);


require __DIR__.'/auth.php';
