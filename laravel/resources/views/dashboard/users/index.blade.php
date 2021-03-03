<?php
    /** @var \App\Models\User[] $users */
?>
<x-dashboard-layout>
    <x-slot name="header">
        {{ trans('messages.users_list') }}
    </x-slot>

    <table class="border-collapse border border-green-800">
        <tbody>
        @each('dashboard.users.blocks.list.item', $users, 'user')
        </tbody>
    </table>
</x-dashboard-layout>
