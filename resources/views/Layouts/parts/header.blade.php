<header class="navbar navbar-expand-md sticky-top d-print-none">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path
                            d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm"
                        style="background-image: url({{ asset("assets/dist/img/user.png") }})">
                        <span class="badge bg-green"></span>
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()?->displayName() }}</div>
                        <div class="mt-1 small text-secondary">{{ auth()->user()->getRoleNames()->implode(" ") }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <!-- <a href="" class="dropdown-item">Change Password</a> -->
                    <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <img src="{{ asset('assets') }}/static/logo.jpg" alt="SPECTA CASE" style="margin: auto;"
                        class="navbar-brand-image">
                    @php($sidebarMenu = App\Helpers\Theme::getMenu())

                    @foreach ($sidebarMenu as $key => $row)
                    @if (!isset($row["children"]))
                        @continue(canViewAny($row))
                    @else
                    <?php        $count = 0; ?>
                    @php($childrenCount = count($row["children"]))
                    @foreach ($row["children"] as $j => $item)
                    @if (canViewAny($item))
                    @if (!isset($item["children"]) || empty($item["children"]))
                        <?php                    $count++;
                        unset($row["children"][$j]); ?>
                    @else
                    @php($subitemCount = 0)
                    @foreach ($item["children"] as $k => $subitem)
                        @if (canViewAny($subitem))
                            @if (!isset($subitem["children"]) || empty($subitem["children"]))
                                <?php                                $subitemCount++;
                                            unset($row["children"][$j]["children"][$k]); ?>
                            @endif
                        @endif
                    @endforeach

                    @if (count($item["children"]) == $subitemCount)
                        <?php                        $count++;
                            unset($row["children"][$j]); ?>
                    @endif
                    @endif
                    @endif
                    @endforeach
                    @if ($childrenCount == $count)
                        @continue
                    @endif
                    @endif
                    @include("components.menu", ["item" => $row])
                    @endforeach

                </ul>
            </div>
        </div>
    </div>
</header>
