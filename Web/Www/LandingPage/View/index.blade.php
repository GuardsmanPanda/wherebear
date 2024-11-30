<?php declare(strict_types=1); ?>
@php use Domain\User\Enum\BearPermissionEnum;use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;use Illuminate\Support\Facades\Session; @endphp
@extends('layout.blank-layout')
@section('styles')
  <style>
    body {
      background-image: url('/static/img/background/mountain.jpg');
      background-size: cover;
    }
  </style>
@endsection
@section('content')
  <div>
    <div class="backdrop-blur-sm backdrop-brightness-[.3]  rounded shadow-md mx-2 my-2 w-[28rem] text-gray-300">
      <div class="flex justify-between select-none">
        <img height="33" width="166" class="my-auto ml-4" src="/static/img/logo/top.png" alt="Awesome Funtime Game">
        <div class="flex">
          @if(BearAuthService::getUserIdOrNull() === null)
            <button
              class="my-3 mr-1 cursor-pointer font-medium px-4 text-lg text-gray-300 transform duration-75 hover:scale-105 hover:underline hover:text-gray-200"
              hx-get="/auth/dialog">Login :D
            </button>
          @else
            @if(BearAuthService::hasPermission(permission: BearPermissionEnum::GAME_CREATE))
              <button class="transform duration-75 hover:scale-110 p-2 hover:text-gray-200" hx-get="/game/create">
                <x-bear::icon name="plus" size="8"/>
              </button>
            @endif
            @if(BearAuthService::hasPermission(permission: BearPermissionEnum::GAME_CREATE_TEMPLATED_GAME))
              <button class="transform duration-75 hover:scale-110 p-2 hover:text-gray-200" hx-get="/game/create-from-template">
                <x-bear::icon name="squares-plus" size="8"/>
              </button>
            @endif
            <button class="transform duration-75 hover:scale-110 p-2 hover:text-green-300 text-green-400" hx-get="/auth/user-settings">
              <x-bear::icon name="cog-6-tooth" size="8"/>
            </button>
          @endif
        </div>
      </div>
      <hr class="border-gray-700">
      @if(Session::has('error'))
        <div class="pr-4 pt-2 text-right font-semibold text-yellow-500">
          {{Session::get('error')}}
        </div>
      @endif
      @if(Session::get("message") !== null)
        <div class="text-center font-medium text-yellow-400 text-lg mt-1">{{ Session::get("message") }}</div>
      @endif
      <div id="game-list" class="px-4 pb-4 pt-2" hx-get="/landing-page/game-list" hx-trigger="load, every 15s"></div>
    </div>

    @if(count($previous_games) > 0)
      <div class="backdrop-blur-sm backdrop-brightness-[.3] rounded shadow-md mx-2 mt-8 mb-2 w-[28rem] text-gray-300">
        <div class="flex justify-between select-none">
          <h2 class="my-2 ml-4 font-medium text-2xl text-blue-500">Completed Games</h2>
        </div>
        <hr class="border-gray-700">
        <div id="previous-games" class="px-4 pb-4 pt-2">
          @foreach($previous_games as $game)
            @if(!$loop->first)
              <hr class="border-gray-600 mt-2 mb-1">
            @endif
            <a class="hover:text-white hover:scale-105 duration-75 transition-transform" href="/game/{{$game->id}}/lobby">
              <h3 class="text-blue-300 text-lg font-medium">{{$game->game_name}}</h3>
              <p class="pl-1 text-sm">Played {{$game->created_at}}</p>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
@endsection
