<?php

use App\Livewire\Employees;
use App\Livewire\Employeecreate;
use App\Livewire\Employeeupdate;

use Livewire\Livewire;

/**
 * 従業員リスト表示テスト
 */
test('renders successfully', function () {
    Livewire::test(Employees::class)
        ->assertStatus(200);
});

/**
 * 従業員 リスト 検索 表示テスト
 */
test('query successfully', function () {
    Livewire::test(Employees::class)
        ->set('search', 'test')
        ->call('changeSearch')
        ->assertStatus(200);
});

/**
 * 従業員 追加 Redirectテスト
 */
test('add employee', function () {
    Livewire::test(Employees::class)
        ->call('newEmployee')
        ->assertRedirect(route('employeecreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 従業員 編集 Redirectテスト
 */
test('edit employee', function () {
    Livewire::test(Employees::class)
        ->call('editEmployee', 1)
        ->assertRedirect(route('employeeupdate', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 従業員 時給 Redirectテスト
 */
test('edit hourlywage', function () {
    Livewire::test(Employees::class)
        ->call('hourlywageEmployee', 1)
        ->assertRedirect(route('hourlywage', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 従業員 追加 表示テスト
 */
test('new employee renders successfully', function () {
    Livewire::test(Employeecreate::class)
        ->assertStatus(200)
        ->assertSee('名前')
        ;
});

/**
 * 従業員追加 登録テスト
 */
test('new employee store', function () {
    Livewire::test(Employeecreate::class)
        ->set('empl_cd', '9999')
        ->set('empl_name_last', '姓')
        ->set('empl_name_first', '名')
        ->set('empl_kana_last', 'セイ')
        ->set('empl_kana_first', 'メイ')
        ->set('empl_hire_date', '2025-01-01')
        ->set('empl_notes', 'テストメモ')
        ->call('storeEmployee')
        ->assertStatus(200)
        ;
});

/**
 * 従業員追加 バリデーションエラー
 */
test('new employee validation error', function () {
    Livewire::test(Employeecreate::class)
        ->call('storeEmployee')
        ->assertHasErrors(['empl_cd', 'empl_name_last']);
});
