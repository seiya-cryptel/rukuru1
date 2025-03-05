<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use App\Http\Middleware\Localization;

// use App\Livewire\Employeeworks;

/**
 * Redirect to the default locale
*/
Route::get('/', function(){
    return redirect('/' . app()->getLocale());
 });

/**
 * resources/mannual マニュアル
 */
Route::group(['middleware' => 'auth'], function () {
    Route::get('/manual/{path?}', function (Request $request,$path='') {
        if($path==='') $path = 'index.html';

        $rp = resource_path('manual/'.$path);
        if(File::exists($rp)){
            return response()->file($rp);
        }else{
            abort(404);
        }
    })->where('path', '.*')
    ->name('manual');
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
        // Route::view('/', 'welcome');

        // トップ
        Route::view('/', 'dashboard')
            ->middleware(['auth', 'verified'])
            ->name('/');

        Route::view('dashboard', 'dashboard')
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        Route::view('test', 'test')
            ->middleware(['auth', 'verified'])
            ->name('test');

        /**
         * Route for the 祝日マスタ
         */
        Route::view('holiday', 'holiday')
            ->middleware(['auth', 'verified'])
            ->name('holiday');
        Route::view('holidaycreate', 'holidaycreate')
            ->middleware(['auth', 'verified'])
            ->name('holidaycreate');
        Route::view('holidayupdate/{id}', 'holidayupdate')
            ->middleware(['auth', 'verified'])
            ->name('holidayupdate');

        /**
         * Route for the 顧客マスタ
         */
        Route::view('client', 'client')
            ->middleware(['auth', 'verified'])
            ->name('client');
        Route::view('clientcreate', 'clientcreate')
            ->middleware(['auth', 'verified'])
            ->name('clientcreate');
        Route::view('clientupdate/{id}', 'clientupdate')
            ->middleware(['auth', 'verified'])
            ->name('clientupdate');

        /**
         * Route for the 顧客部門マスタ
         */
        Route::view('clientplace', 'clientplace')
            ->middleware(['auth', 'verified'])
            ->name('clientplace');
        Route::view('clientplacecreate', 'clientplacecreate')
            ->middleware(['auth', 'verified'])
            ->name('clientplacecreate');
        Route::view('clientplaceupdate/{id}', 'clientplaceupdate')
            ->middleware(['auth', 'verified'])
            ->name('clientplaceupdate');

        /**
         * Route for the 作業種別マスタ
         */
        Route::view('clientworktype', 'clientworktype')
            ->middleware(['auth', 'verified'])
            ->name('clientworktype');
        Route::view('clientworktypecreate', 'clientworktypecreate')
            ->middleware(['auth', 'verified'])
            ->name('clientworktypecreate');
        Route::view('clientworktypeupdate/{id}', 'clientworktypeupdate')
            ->middleware(['auth', 'verified'])
            ->name('clientworktypeupdate');

        /**
         * Route for the 手当控除項目マスタ
         */
        Route::view('masterallowdeduct', 'masterallowdeduct')
            ->middleware(['auth', 'verified'])
            ->name('masterallowdeduct');
        Route::view('allowdeductcreate', 'allowdeductcreate')
            ->middleware(['auth', 'verified'])
            ->name('allowdeductcreate');
        Route::view('allowdeductupdate/{id}', 'allowdeductupdate')
            ->middleware(['auth', 'verified'])
            ->name('allowdeductupdate');

        /**
         * Route for the 従業員マスタ
         */
        Route::view('employee', 'employee')
            ->middleware(['auth', 'verified'])
            ->name('employee');
        Route::view('employeecreate', 'employeecreate')
            ->middleware(['auth', 'verified'])
            ->name('employeecreate');
        Route::view('employeeupdate/{id}', 'employeeupdate')
            ->middleware(['auth', 'verified'])
            ->name('employeeupdate');

        /**
         * Route for the 従業員時給マスタ
         */
        Route::view('hourlywage/{id}', 'hourlywage')
            ->middleware(['auth', 'verified'])
            ->name('hourlywage');
        Route::view('hourlywagecreate/{employee_id}', 'hourlywagecreate')
            ->middleware(['auth', 'verified'])
            ->name('hourlywagecreate');
        Route::view('hourlywageupdate/{employee_id}/{employeepay_id}', 'hourlywageupdate')
            ->middleware(['auth', 'verified'])
            ->name('hourlywageupdate');

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
        // Route::view('employeework/', 'employeework')
        Route::view('employeework/{workYear}/{workMonth}/{clientId}/{clientPlaceId}/{employeeId}', 'employeework')
            ->middleware(['auth', 'verified'])
            ->name('employeework');
        Route::view('employeeworksone/{workYear}/{workMonth}/{clientId}/{clientPlaceId}/{employeeId}', 'employeeworksone')
            ->middleware(['auth', 'verified'])
            ->name('employeeworksone');
        Route::view('employeeworksslot/{workYear}/{workMonth}/{clientId}/{clientPlaceId}/{employeeId}', 'employeeworksslot')
            ->middleware(['auth', 'verified'])
            ->name('employeeworksslot');

        /**
         * Routes for the 勤怠詳細レポート
         */
        Route::view('reportkintaidetails', 'reportkintaidetails')
            ->middleware(['auth', 'verified'])
            ->name('reportkintaidetails');

        /**
         * Routes for the 勤怠エントリー 請求締め
         */
        Route::view('closebills', 'closebills')
            ->middleware(['auth', 'verified'])
            ->name('closebills');

        /**
         * Routes for the 勤怠エントリー 給与締め
         */
        Route::view('closepayrolls', 'closepayrolls')
            ->middleware(['auth', 'verified'])
            ->name('closepayrolls');

        /**
         * Routes for the 請求　請求書出力
         */
        Route::view('bills', 'bills')
            ->middleware(['auth', 'verified'])
            ->name('bills');

        /**
         * Routes for the 請求　請求明細表示
         */
        Route::view('billdetails/{billId}', 'billdetails')
            ->middleware(['auth', 'verified'])
            ->name('billdetails');

        /**
         * Routes for the 給与　手当控除入力
         */
        Route::view('salaryemployee', 'salaryemployee')
            ->middleware(['auth', 'verified'])
            ->name('salaryemployee');
    
        /**
        * Routes for the 手当控除入力 個人別  
        */
        Route::view('employeesalary/{workYear}/{workMonth}/{employeeId}', 'employeesalary')
            ->middleware(['auth', 'verified'])
            ->name('employeesalary');

        /**
         * Routes for the 給与　給与計算
         */
        Route::view('closesalaries', 'closesalaries')
            ->middleware(['auth', 'verified'])
            ->name('closesalaries');
    
        /**
         * ユーザ プロファイル
         */
        Route::view('profile', 'profile')
            ->middleware(['auth'])
            ->name('profile');
}
);


require __DIR__.'/auth.php';
