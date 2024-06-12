<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\Localization;

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
         * Routes for the 勤怠インポート
         */
        Route::view('importkintai', 'importkintai')
            ->middleware(['auth', 'verified'])
            ->name('importkintai');

        /**
         * Routes for the 勤怠エントリー 従業員一覧
         */
        Route::view('kintaientry', 'kintaientry')
            ->middleware(['auth', 'verified'])
            ->name('kintaientry');
    
        /**
        * Routes for the 勤怠エントリー 個人別  
        */
        Route::view('kintaipersonday', 'kintaipersonday')
            ->middleware(['auth', 'verified'])
            ->name('kintaipersonday');

        /**
         * Routes for the 勤怠エントリー 勤怠締め
         */
        Route::view('kintaiclose', 'kintaiclose')
            ->middleware(['auth', 'verified'])
            ->name('kintaiclose');

        /**
         * Routes for the 請求　請求書出力
         */
        Route::view('billexport', 'billexport')
            ->middleware(['auth', 'verified'])
            ->name('billexport');

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

        /**
         * Routes for the マスタ　顧客
         */
        Route::view('masterclient', 'masterclient')
            ->middleware(['auth', 'verified'])
            ->name('masterclient');

        /**
         * Routes for the マスタ　顧客　編集
         */
        Route::view('masterclientedit', 'masterclientedit')
            ->middleware(['auth', 'verified'])
            ->name('masterclientedit');
    
        Route::view('profile', 'profile')
            ->middleware(['auth'])
            ->name('profile');
}
);


require __DIR__.'/auth.php';
