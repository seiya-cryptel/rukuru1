<nav class="-mx-3 {{-- 外側横余白0.75rem --}}
     flex 
     flex-1 {{-- flex-grow, flex-shrink, flex-basisの値をそれぞれ1, 1, 0% --}}
     justify-end {{-- X軸終端に配置 --}}">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md {{-- 要素の角を丸くする --}}
                 px-3 py-2 text-black 
                 ring-1 {{-- 外枠 ring 描画 --}} 
                 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
        <?php echo app()->getLocale(); ?>
            {{ __('Log in') }}
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Register
            </a>
        @endif
    @endauth

    <div class="gap-2 px-3 py-2 text-black">
        <a href="/setlocale/ja">Jpn</a>
        <a href="/setlocale/en">Eng</a>
    </div>

</nav>
