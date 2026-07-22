<aside class="sidebar">
    <div class="sidebar__backdrop"></div>
    <div class="sidebar__container">

        <div class="sidebar__top">
            <div class="container container--sm text-center">
                <a class="sidebar__logo d-inline-block" href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/img/content/wonderparklogo.png') }}" alt="logo" class="img-fluid"
                        style="max-height: 70px; width: auto;" />
                </a>
            </div>
        </div>

        <div class="sidebar__content" data-simplebar="data-simplebar">
            <nav class="sidebar__nav">
                <ul class="sidebar__menu">
                    <li class="sidebar__menu-item"><a class="sidebar__link" href="{{ route('dashboard') }}"
                            aria-expanded="true"><span class="sidebar__link-icon">
                                <svg class="icon-icon-dashboard">
                                    <use xlink:href="#icon-dashboard"></use>
                                </svg></span><span class="sidebar__link-text">Dashboard</span></a>
                    </li>

                    <li class="sidebar__menu-item"><a class="sidebar__link" href="" data-toggle="collapse"
                            data-target="#Products" aria-expanded="true"><span class="sidebar__link-icon">
                                <svg class="icon-icon-cart">
                                    <use xlink:href="#icon-cart"></use>
                                </svg></span><span class="sidebar__link-text">Coupon</span><span
                                class="sidebar__link-arrow">
                                <svg class="icon-icon-keyboard-down">
                                    <use xlink:href="#icon-keyboard-down"></use>
                                </svg></span></a>
                        <div class="collapse {{ url()->current() == route('couponserials.index') || url()->current() == route('couponserials.create') ? ' show' : '' }}"
                            id="Products">
                            <ul class="sidebar__collapse-menu">
                                
                                <li class="sidebar__menu-item"><a
                                        class="sidebar__link {{ url()->current() == route('couponserials.index') ? ' active' : '' }}"
                                        href="{{ route('couponserials.index') }}"><span
                                            class="sidebar__link-signal"></span><span class="sidebar__link-text">Coupon
                                            Serial</span></a>
                                </li>
                               

                            </ul>
                        </div>
                    </li>



                    
                    
                    
                    


                    @if (Gate::check('view user') || Gate::check('view role') || Gate::check('view permission'))
                        <li class="sidebar__menu-item"><a class="sidebar__link" href=""
                                data-toggle="collapse" data-target="#Settings" aria-expanded="false"><span
                                    class="sidebar__link-icon">
                                    <svg class="icon-icon-cart">
                                        <use xlink:href="#icon-cart"></use>
                                    </svg></span><span class="sidebar__link-text">Settings</span><span
                                    class="sidebar__link-arrow">
                                    <svg class="icon-icon-keyboard-down">
                                        <use xlink:href="#icon-keyboard-down"></use>
                                    </svg></span></a>
                            <!--<div class="collapse" id="Settings">-->
                            <div class="collapse {{ url()->current() == route('users.index') ? ' show' : '' }}"
                                id="Settings">

                                <ul class="sidebar__collapse-menu">
                                    <li class="sidebar__menu-item"><a
                                            class="sidebar__link {{ url()->current() == route('users.index') ? ' active' : '' }}"
                                            href="{{ route('users.index') }}"><span
                                                class="sidebar__link-signal"></span><span
                                                class="sidebar__link-text">Users</span></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif



                </ul>
            </nav>
        </div>
    </div>
</aside>
