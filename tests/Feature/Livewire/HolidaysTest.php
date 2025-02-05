<?php

use App\Livewire\Holidays;
use App\Livewire\Holidaycreate;
use App\Livewire\Holidayupdate;
use App\Models\holiday;
use Livewire\Livewire;
// use Mockery;

/**
 * 祝日リスト表示テスト
 */
test('renders successfully', function () {
    Livewire::test(Holidays::class)
        ->assertStatus(200)
        ->assertSeeLivewire(Holidays::class);
});

/**
 * 祝日追加Redirectテスト
 */
test('add holiday', function () {
    Livewire::test(Holidays::class)
        ->call('newHoliday')
        ->assertRedirect(route('holidaycreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 祝日編集Redirectテスト
 */
test('edit holiday', function () {
    Livewire::test(Holidays::class)
        ->call('editHoliday', 1)
        ->assertRedirect(route('holidayupdate', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 祝日追加 表示テスト
 */
test('new holiday renders successfully', function () {
    Livewire::test(Holidaycreate::class)
        ->assertStatus(200)
        // ->assertSeeLivewire(Holidaycreate::class)
        ->assertSee('祝日名前')
        ;
});

/**
 * 祝日追加 登録テスト
 */
test('new holiday store', function () {
    Livewire::test(Holidaycreate::class)
        ->set('holiday_date', '2021-12-31')
        ->set('client_id', 1)
        ->set('holiday_name', 'テスト祝日')
        ->set('notes', 'テストメモ')
        ->call('storeHoliday')
        ->assertStatus(200)
        ;
});

/**
 * 祝日追加 バリデーションエラー
 */
test('new holiday validation error', function () {
    Livewire::test(Holidaycreate::class)
        ->call('storeHoliday')
        ->assertHasErrors(['holiday_date', 'holiday_name']);
});

/**
 * 祝日更新 表示テスト
 */
/*
test('update holiday renders successfully', function () {
    // モックを作成
    $mockHoliday = Mockery::mock(holiday::class);
    $mockHoliday->shouldReceive('find')
        // ->once()
        ->with(1)
        ->andReturn((object)[
        'id' => 1,
        'holiday_date' => '2023-01-01',
        'client_id' => 0,
        'holiday_name' => 'Test holiday',
        'notes' => 'Test Notes',
    ]);
    // モックを注入
    // $this->instance(holiday::class, $mockHoliday);
    $this->app->instance(holiday::class, $mockHoliday);

    Livewire::test(Holidayupdate::class, ['id' => 1])
        ->assertStatus(200)
        // ->assertSeeLivewire(Holidaycreate::class)
        ->assertSee('祝日名前')
        ;
});
*/