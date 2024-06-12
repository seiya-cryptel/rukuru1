<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('RUKURU') }} {{ __('Work Hours') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">{{-- フォント全般の設定 --}}
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">{{-- 背景色全般の設定 --}}
            <div class="relative {{-- 位置相対 --}}
                 min-h-screen {{-- 最小高さビューポート（全画面）分 --}}
                 flex {{-- フレックスコンテナ 自動サイズ調整＆配置 --}}
                 flex-col {{-- 主軸は縦 --}}
                 items-center {{-- 交差軸の中央に子要素を配置 --}}
                 justify-center {{-- 主軸の中央に子要素を配置 --}}
                 selection:bg-[#FF2D20] {{-- 選択範囲の背景色 --}}
                 selection:text-white {{-- 選択範囲の文字色 --}}">
                <div class="relative w-full {{-- 横幅一杯 --}}
                     max-w-2xl {{-- 最大幅42rem --}}
                     px-6 {{-- 内側横余白1.5rem --}}
                     lg:max-w-7xl {{-- ビューポートサイズ lg(>1024px) のとき: 最大幅80rem --}}">
                    <header class="grid {{-- グリッドコンテナの宣言 --}}
                         grid-cols-2 {{-- グリッドは2列 --}}
                         items-center
                         gap-2 {{-- アイテム間隔1.5rem --}}
                         py-10 {{-- 内側縦余白2.5rem --}}
                         lg:grid-cols-3 {{-- lg: グリッドは3列 --}}">
                         <div class="px-3 py-2 text-black">
                         {{ __('RUKURU') }} {{ __('Work Hours') }}
                        </div>
                        <livewire:welcome.navigation />
                    </header>

                    <main class="mt-6 {{-- 外側上余白1.5rem --}}">
                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">

                            <div class="flex lg:justify-center lg:col-start-2">
                                
                            </div>

                        </div>
                    </main>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        <a href="https://cryptel.co.jp/" target="_blank" class="hover:text-green focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-green">
                            by <span style="color: darkgreen">RUKURU</span> and <span style="color: green">Crypt</span><span style="color: gray">Ang</span><span style="color: green">el</span>
                        </a>
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
