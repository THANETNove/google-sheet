<aside id="layout-menu" class="layout-menu   menu-vertical menu bg-menu-theme">
    <div id="layout-menu2">
        <div class="app-brand demo">
            <img src="{{ URL::asset('/assets/img/icons/icon.png') }}" class="icon-aside">
            <span class="app-brand-text  menu-text fw-bolder ms-2 text-gl-aside">บัญชีเเยกประเภท</span>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>

        <div class="menu-inner-shadow"></div>

        {{--  ส่วนของเมนู admin --}}
        @if (Auth::check())
            <ul class="menu-inner py-1">

                @if (Auth::user()->status == 1)
                    <!-- Dashboard -->
                    <li class="menu-item   {{ Request::is('home') || Request::is('company*') ? 'active' : '' }} ">
                        <a href="{{ url('home') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard </div>
                        </a>
                    </li>
                @endif




                <!--รายงาน -->
                @if (session()->has('company_id'))
                    <li class="menu-item  {{ Request::is('report/*') ? 'active open' : '' }} ">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons bx bxs-report'></i>
                            <div data-i18n="Layouts">รายงาน</div>
                        </a>

                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Request::is('report/general-journal-view/*') || Request::is('report/search-date') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/general-journal-view', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">สมุดรายวันทั่วไป</div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Request::is('report/ledger/*') || Request::is('report/search-ledger') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/ledger', session()->get('company_id')) }} @else # @endif "
                                    class="menu-link">
                                    <div data-i18n="Without menu">สมุดบัญชีแยกประเภท</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Request::is('report/buy-view/*') || Request::is('report/search-buy') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/buy-view', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">รายงานภาษีซื้อ</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Request::is('report/sell-view/*') || Request::is('report/search-sell') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/sell-view', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">รายงานภาษีขาย</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Request::is('report/account-balance-sheet/*') || Request::is('report/search-account-balance-sheet') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/account-balance-sheet', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">งบดุลบัญชี</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Request::is('report/profit-statement/*') || Request::is('report/search-profit-statement') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/profit-statement', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">งบกำไร(ขาดทุน)</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Request::is('report/trial-balance-before-closing/*') || Request::is('report/search-trial-balance-before-closing') ? 'active' : '' }} ">
                                <a href="@if (session()->has('company_id')) {{ url('report/trial-balance-before-closing', session()->get('company_id')) }} @else # @endif"
                                    class="menu-link">
                                    <div data-i18n="Without menu">งบทดลองก่อนปิดบัญชี</div>
                                </a>
                            </li>

                        </ul>
                    </li>

                    @if (Auth::user()->status == 1)
                        <!-- อัพข้อมูล-ลงบัญชีอัตโนมัต -->
                        <li class="menu-item  {{ Request::is('update/*') ? 'active open' : '' }} ">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class='menu-icon tf-icons bx bxs-server'></i>
                                <div data-i18n="Layouts">อัพข้อมูล-ลงบัญชี</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ Request::is('information/*') ? 'active' : '' }} ">
                                    <a href="{{ url('#') }} " class="menu-link">
                                        <div data-i18n="Without menu">รายงานภาษีขาย-ซื้อ</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('information/*') ? 'active' : '' }} ">
                                    <a href="{{ url('#') }} " class="menu-link">
                                        <div data-i18n="Without menu">ภาษีหัก ณ ที่จ่าย</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('information/*') ? 'active' : '' }} ">
                                    <a href="{{ url('#') }} " class="menu-link">
                                        <div data-i18n="Without menu">เงินเดือนประกันสังคม</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('information/*') ? 'active' : '' }} ">
                                    <a href="{{ url('#') }} " class="menu-link">
                                        <div data-i18n="Without menu">ภาษีมูลค่าเพิ่ม</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('update/import-data/*') ? 'active' : '' }}">
                                    <a href="@if (session()->has('company_id')) {{ url('update/import-data', session()->get('company_id')) }} @else # @endif"
                                        class="menu-link">
                                        <div data-i18n="Without menu">Googlesheet เข้าระบบ Mysql</div>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif


                @endif
            </ul>
        @endif


        @if (!Auth::check() && session()->has('company_status') && session('company_status') == 0)
            <ul class="menu-inner py-1">

                <!--รายงาน -->

                <li class="menu-item  {{ Request::is('report/*', 'user-report/*') ? 'active open' : '' }} ">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bxs-report'></i>
                        <div data-i18n="Layouts">รายงาน</div>
                    </a>

                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ Request::is('user-report/general-journal*', 'user-report/search-date*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/general-journal') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">สมุดรายวันทั่วไป</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ Request::is('report/ledger/*', 'report/search-ledger', 'user-report/ledger*', 'user-report/search-ledger*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/ledger') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">สมุดบัญชีแยกประเภท</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/buy-view/*', 'report/search-buy', 'user-report/buy*', 'user-report/search-buy*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/buy') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">รายงานภาษีซื้อ</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/sell-view/*', 'report/search-sell', 'user-report/sell*', 'user-report/search-sell*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/sell') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">รายงานภาษีขาย</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/account-balance-sheet/*', 'report/search-account-balance-sheet', 'user-report/account-balance-sheet*', 'user-report/search-account-balance-sheet*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/account-balance-sheet') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">งบดุลบัญชี</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/profit-statement/*', 'report/search-profit-statement', 'user-report/profit-statement*', 'user-report/search-profit-statement*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/profit-statement') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">งบกำไร(ขาดทุน)</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/trial-balance-before-closing/*', 'report/search-trial-balance-before-closing', 'user-report/trial-balance-before-closing*', 'user-report/search-trial-balance-before-closing*') ? 'active' : '' }} ">
                            <a href="{{ url('user-report/trial-balance-before-closing') . '?username=' . urlencode($user->username) . '&password=' . urlencode($user->password) }}"
                                class="menu-link">
                                <div data-i18n="Without menu">งบทดลองก่อนปิดบัญชี</div>
                            </a>
                        </li>

                    </ul>
                </li>



            </ul>
        @endif

    </div>
</aside>
