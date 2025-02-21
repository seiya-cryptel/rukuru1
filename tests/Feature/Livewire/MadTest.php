<?php

use App\Livewire\Masterallowdeducts;
use App\Livewire\Allowdeductcreate;
use App\Livewire\Allowdeductupdate;

use Livewire\Livewire;

/**
 * 手当控除リスト表示テスト
 */
test('renders successfully', function () {
    Livewire::test(Masterallowdeducts::class)
        ->assertStatus(200);
});

/**
 * 手当控除追加Redirectテスト
 */
test('add item', function () {
    Livewire::test(Masterallowdeducts::class)
        ->call('newMad')
        ->assertRedirect(route('allowdeductcreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 手当控除編集Redirectテスト
 */
test('edit item', function () {
    Livewire::test(Masterallowdeducts::class)
        ->call('editMad', 8)
        ->assertRedirect(route('allowdeductupdate', ['locale' => app()->getLocale(), 'id' => 8], absolute: false));
});

/**
 * 手当控除追加 表示テスト
 */
test('new item renders successfully', function () {
    Livewire::test(Allowdeductcreate::class)
        ->assertStatus(200)
        ->assertSee('コード')
        ;
});

/**
 * 手当控除追加 登録テスト
 */
test('new item store', function () {
    Livewire::test(Allowdeductcreate::class)
        ->set('mad_cd', '9999')
        ->set('mad_allow', '1')
        ->set('mad_deduct', '0')
        ->set('mad_name', 'テスト手当')
        ->set('mad_notes', 'テストメモ')
        ->call('storeMad')
        ->assertStatus(200)
        ;
});

/**
 * 手当控除追加 バリデーションエラー
 */
test('new item validation error', function () {
    Livewire::test(Allowdeductcreate::class)
        ->call('storeMad')
        ->assertHasErrors(['mad_cd', 'mad_name']);
});
