<?php
    /** @var \App\Models\Role[] $roles */
?>
<x-dashboard-layout>
    <x-slot name="header">
        {{ trans('messages.roles_list') }}
    </x-slot>

    <table class="border-collapse border border-green-800">
        <tbody>
        @each('dashboard.roles.blocks.list.item', $roles, 'role')
        </tbody>
    </table>
</x-dashboard-layout>
