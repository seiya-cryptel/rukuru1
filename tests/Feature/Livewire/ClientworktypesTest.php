<?php

use App\Livewire\Clientworktypes;
use Livewire\Livewire;

/**
 * 作業種別リスト 表示テスト
 */
it('renders successfully', function () {
    Livewire::test(Clientworktypes::class)
        ->assertStatus(200);
});

/**
 * 作業種別 追加 Redirectテスト
 */
test('add client work type', function () {
    Livewire::test(Clientworktypes::class)
        ->call('newClientWorkType')
        ->assertRedirect(route('clientworktypecreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 作業種別 編集 Redirectテスト
 */
test('edit client work type', function () {
    Livewire::test(Clientworktypes::class)
        ->call('editClientWorkType', 1)
        ->assertRedirect(route('clientworktypeupdate', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 作業種別 追加 表示テスト
 */
test('new client work type renders successfully', function () {
    Livewire::test(Clientcreate::class)
        ->assertStatus(200)
        // ->assertSeeLivewire(Clientcreate::class)
        ->assertSee('顧客名前')
        ;
});

/**
 * 作業種別 追加 登録テスト
 */
test('new client work type store', function () {
    Livewire::test(Clientworktypecreate::class)
        ->set('client_id', '3')
        ->set('clientplace_id', '3')

        ->set('wt_cd', 'xx')
        ->set('wt_name', '業務種別名')
        ->set('wt_kana', '業務種別かな')
        ->set('wt_alpha', '業務種別英字')

        ->set('wt_day_night', '1')      // 日勤
        ->set('wt_work_start', '08:00') // 勤務開始時刻
        ->set('wt_work_end', '17:00')   // 勤務終了時刻

        ->set('wt_lunch_break_start', '12:00') // 昼休憩開始時刻
        ->set('wt_lunch_break_end', '13:00')   // 昼休憩終了時刻
        ->set('wt_evening_break_start', '17:00') // 夕休憩開始時刻
        ->set('wt_evening_break_end', '17:30')   // 夕休憩終了時刻

        ->set('wt_pay_std', '1000') // 標準時給
        ->set('wt_pay_ovr', '1250') // 残業時給

        ->set('wt_bill_std', '1200')    // 標準請求単価
        ->set('wt_bill_ovr', '1500')    // 残業請求単価

        ->call('storeClientWorkType')
        ->assertStatus(200)
        ;
});

/**
 * 作業種別 追加 バリデーションエラー
 */
test('new client work type validation error', function () {
    Livewire::test(Clientworktypecreate::class)
        ->call('storeClientWorkType')
        ->assertHasErrors(['wt_cd', 'wt_name']);
});
