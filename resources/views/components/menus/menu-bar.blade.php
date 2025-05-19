<li class="nav-item">
    <a href="{{$route}}" class="nav-link">
        <i class="nav-icon {{$icon}}"></i>
        <p>
            {{$name}}
            @if($slot!="")
                <i class="right fas fa-angle-left"></i>
            @endif
        </p>
    </a>
    @if($slot!="")
        <ul class="nav nav-treeview">
            {{$slot}}
        </ul>
    @endif
</li>
