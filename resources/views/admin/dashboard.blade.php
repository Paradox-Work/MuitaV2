@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">👑 Admin Dashboard</h1>
                <p class="text-gray-600 mb-6">Full system access and administration</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-800">Total Users</h3>
                        <p class="text-2xl">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-800">Total Cases</h3>
                        <p class="text-2xl">{{ \App\Models\Cases::count() ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-purple-800">Active Roles</h3>
                        <p class="text-2xl">{{ \Spatie\Permission\Models\Role::count() }}</p>
                    </div>
                </div>
                  @if($user_role === 'admin')
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>               
                    <a href="/admin/cases" class="btn btn-primary">View All Cases</a>
                    <a href="/admin/users" class="btn btn-secondary">System Users (API)</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection