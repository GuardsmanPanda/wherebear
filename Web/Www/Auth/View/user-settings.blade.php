<?php declare(strict_types=1); ?>
@php use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService; @endphp

<x-bear::dialog.basic>
    <form class="flex gap-4 items-end" hx-patch="/auth/user-settings" autocomplete="off">
        <x-bear::form.text id="display_name" required maxlength="32">{{BearAuthService::getUser()->display_name}}</x-bear::form.text>
        <x-bear::button.dark type="submit">Update</x-bear::button.dark>
    </form>
    <a href="/bear/auth/sign-out"><x-bear::button.dark class="mt-4" color="red">Logout</x-bear::button.dark></a>
</x-bear::dialog.basic>
