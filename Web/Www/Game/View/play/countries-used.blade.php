<?php declare(strict_types=1); ?>
@if(count($countries_used) > 0)
    <div class="absolute bg-black bg-opacity-70 font-bold bottom-0 text-gray-300 text-xl shadow-lg flex items-center"
         style="z-index: 500;">
        <div class="px-2">Out</div>
        @foreach($countries_used as $o)
            <img class="h-9" src="/static/flag/svg/{{$o->cca2}}.svg" alt="Country flag" tippy="{{$o->name}}">
        @endforeach
    </div>
@endif