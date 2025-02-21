<?php

use App\Livewire\Clientplaces;
use App\Livewire\Clientplacecreate;
use App\Livewire\Clientplaceupdate;

use Livewire\Livewire;

/**
 * 顧客部門リスト 表示テスト
 */
it('renders successfully', function () {
    Livewire::test(Clientplaces::class)
        ->assertStatus(200);
});

/**
 * 顧客部門 追加 Redirectテスト
 */
test('add client place', function () {
    Livewire::test(Clientplaces::class)
        ->call('newClientPlace')
        ->assertRedirect(route('clientplacecreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 顧客部門 編集 Redirectテスト
 */
test('edit client place', function () {
    Livewire::test(Clientplaces::class)
        ->call('editClientPlace', 1)
        ->assertRedirect(route('clientplaceupdate', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 顧客部門 追加 表示テスト
 */
test('new client place renders successfully', function () {
    Livewire::test(Clientcreate::class)
        ->assertStatus(200)
        // ->assertSeeLivewire(Clientcreate::class)
        ->assertSee('顧客名前')
        ;
});

/**
 * 顧客部門 追加 登録テスト
 */
test('new client place store', function () {
    Livewire::test(Clientplacecreate::class)
        ->set('client_id', '3')
        ->set('cl_pl_cd', '99')
        ->set('cl_pl_name', 'テスト部門')
        ->set('cl_pl_kana', 'テストジギョウショ')
        ->set('cl_pl_alpha', 'test client place')
        ->set('cl_pl_notes', 'テストメモ')
        ->call('storeClientPlace')
        ->assertStatus(200)
        ;
});

/**
 * 顧客部門 追加 バリデーションエラー
 */
test('new client place validation error', function () {
    Livewire::test(Clientplacecreate::class)
        ->call('storeClientPlace')
        ->assertHasErrors(['client_id', 'cl_pl_cd', 'cl_pl_name']);
});
