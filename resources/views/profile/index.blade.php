{{-- @extends('layouts.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h1>{{ __('Profile') }}</h1>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Field') }}</th>
                        <th>{{ __('Value') }}</th>
                    </tr>
                </thead>
                <div class="tbody">
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Email') }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Created At') }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Updated At') }}</td>
                        <td>{{ $user->updated_at }}</td>
                    </tr>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Index') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
