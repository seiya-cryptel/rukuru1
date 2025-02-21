<?php

use App\Livewire\Client;
use App\Livewire\Clientcreate;
use App\Livewire\Clientupdate;

use Livewire\Livewire;

/**
 * 顧客リスト表示テスト
 */
test('renders successfully', function () {
    Livewire::test(Client::class)
        ->assertStatus(200);
});

/**
 * 顧客追加Redirectテスト
 */
test('add client', function () {
    Livewire::test(Client::class)
        ->call('newClient')
        ->assertRedirect(route('clientcreate', ['locale' => app()->getLocale()], absolute: false));
});

/**
 * 顧客編集Redirectテスト
 */
test('edit client', function () {
    Livewire::test(Client::class)
        ->call('editClient', 1)
        ->assertRedirect(route('clientupdate', ['locale' => app()->getLocale(), 'id' => 1], absolute: false));
});

/**
 * 顧客追加 表示テスト
 */
test('new client renders successfully', function () {
    Livewire::test(Clientcreate::class)
        ->assertStatus(200)
        // ->assertSeeLivewire(Clientcreate::class)
        ->assertSee('顧客名前')
        ;
});

/**
 * 顧客追加 登録テスト
 */
test('new client store', function () {
    Livewire::test(Clientcreate::class)
        ->set('cl_cd', '9999')
        ->set('cl_name', 'テスト顧客')
        ->set('cl_kana', 'テストコキャク')
        ->set('cl_alpha', 'test client')
        ->set('cl_round_start', '15')
        ->set('cl_round_end', '5')
        ->set('cl_notes', 'テストメモ')
        ->call('storeClient')
        ->assertStatus(200)
        ;
});

/**
 * 顧客追加 バリデーションエラー
 */
test('new client validation error', function () {
    Livewire::test(Clientcreate::class)
        ->call('storeClient')
        ->assertHasErrors(['cl_cd', 'cl_name']);
});
