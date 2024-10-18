<aside id="layout-menu" class="layout-menu   menu-vertical menu bg-menu-theme">
    <div id="layout-menu2">
        <div class="app-brand demo">
            <img src="{{ URL::asset('/assets/img/icons/icon.png') }}" class="icon-aside">
            <span class="app-brand-text  menu-text fw-bolder ms-2 text-gl-aside">บัญชีเเยกประเภท</span>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item   {{ Request::is('home') || Request::is('company*') ? 'active' : '' }} ">
                <a href="{{ url('home') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard </div>
                </a>
            </li>


            <!-- บริษัท -->
            {{--  <li class="menu-item  {{ Request::is('company*') ? 'active open' : '' }} ">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-layout"></i>
                    <div data-i18n="Layouts">บริษัท</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ Request::is('company*') ? 'active' : '' }} ">
                        <a href="{{ url('company') }} " class="menu-link">
                            <div data-i18n="Without menu">รายชื่อบริษัท</div>
                        </a>
                    </li>

                </ul>
            </li>
 --}}

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
                        <li class="menu-item {{ Request::is('report/ledger-book') ? 'active' : '' }} ">
                            <a href="{{ url('company') }} " class="menu-link">
                                <div data-i18n="Without menu">สมุดบัญชีแยกประเภท</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/buy-view/*') || Request::is('report/search-buy') ? 'active' : '' }} ">
                            <a href="@if (session()->has('company_id')) {{ url('report/buy-view', session()->get('company_id')) }} @else # @endif"
                                class="menu-link">
                                <div data-i18n="Without menu">สมุดบัญชีรายงานซื้อ</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('report/sell-view/*') || Request::is('report/search-sell') ? 'active' : '' }} ">
                            <a href="@if (session()->has('company_id')) {{ url('report/sell-view', session()->get('company_id')) }} @else # @endif"
                                class="menu-link">
                                <div data-i18n="Without menu">สมุดบัญชีรายงานขาย</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('repor') ? 'active' : '' }} ">
                            <a href="{{ url('company') }} " class="menu-link">
                                <div data-i18n="Without menu">งบดุลบัญชี</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('report') ? 'active' : '' }} ">
                            <a href="{{ url('company') }} " class="menu-link">
                                <div data-i18n="Without menu">งบกำไร(ขาดทุน)</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('report') ? 'active' : '' }} ">
                            <a href="{{ url('company') }} " class="menu-link">
                                <div data-i18n="Without menu">งบทดลองก่อนปิดบัญชี</div>
                            </a>
                        </li>

                    </ul>
                </li>

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
                        <li class="menu-item {{ Request::is('update/import-data/*') ? 'active' : '' }}">
                            <a href="@if (session()->has('company_id')) {{ url('update/import-data', session()->get('company_id')) }} @else # @endif"
                                class="menu-link">
                                <div data-i18n="Without menu">Googlesheet เข้าระบบ Mysql</div>
                            </a>
                        </li>

                    </ul>
                </li>
            @endif
        </ul>
    </div>
</aside>
