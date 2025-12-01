@php
    $isActive = $item["active"] ?? false;

@endphp
@if (!isset($item["children"]))
    <li class="nav-item {{ $isActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ $item['url'] }}" >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="{{ $item['icon'] ?? '' }}"> </i>
            </span>
            <span class="nav-link-title">
                {{ $item["name"] }}
            </span>
        </a>
    </li>
@else
<li class="nav-item dropdown {{ $isActive ? 'active' : '' }}">
    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
      <span class="nav-link-icon d-md-none d-lg-inline-block">
        <i class="{{ $item['icon'] ?? '' }}"></i>
      </span>
      <span class="nav-link-title">
        {{ $item["name"] }}
      </span>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow">
      <div class="dropdown-menu-columns">
        <div class="dropdown-menu-column">

            @foreach($item['children'] as $key => $child)
            @if (!isset($child['children']))
                <a class="dropdown-item {{ $child['active'] ? 'active' : '' }}" href="{{ $child['url'] }}">
                    @if(isset($child['icon']))
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <i class="{{ $child['icon']  }}" aria-hidden="true"></i>
                    </span>
                    @endif
                    {{ $child['name'] }}
                    @isset($child['count'])
                        <span class="badge bg-primary rounded-pill ms-auto">{{ $child['count'] }}</span>
                    @endisset
                </a>
            @else
                <div class="dropend">
                  <a class="dropdown-item dropdown-toggle {{ $child['active'] ? 'active' : '' }}" href="{{ $child['url'] }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                    @if(isset($child['icon']))
                      <span class="nav-link-icon d-md-none d-lg-inline-block">
                          <i class="{{ $child['icon']  }}" aria-hidden="true"></i>
                      </span>
                    @endif
                    {{ $child['name'] }}
                    @isset($child['count'])
                        <span class="badge bg-primary rounded-pill ms-auto">{{ $child['count'] }}</span>
                    @endisset
                  </a>
                  <div class="dropdown-menu ">
                    @foreach($child['children'] as $key => $subchild)
                      <a href="{{ $subchild['url'] }}" class="dropdown-item {{ $subchild['active'] ? 'active' : '' }}">
                        @if(isset($subchild['icon']))
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="{{ $subchild['icon']  }}" aria-hidden="true"></i>
                        </span>
                        @endif
                        {{ $subchild['name'] }}
                        @isset($subchild['count'])
                            <span class="badge bg-primary rounded-pill ms-auto">{{ $subchild['count'] }}</span>
                        @endisset
                      </a>
                      @endforeach
                  </div>
                </div>
            @endif
            @endforeach
        </div>
      </div>
    </div>
</li>
@endif
