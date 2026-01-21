@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📄 Broker Dashboard</h1>
                <p class="text-gray-600 mb-6">Submit and track customs declarations</p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-800 mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="text-blue-700">As a customs broker, you can submit new declarations and track their processing status.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">My Cases</h3>
                        <p class="text-gray-600 mb-3">View and manage your submitted declarations</p>
                        <a href="/broker/cases" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            View My Cases
                        </a>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">New Declaration</h3>
                        <p class="text-gray-600 mb-3">Submit a new customs declaration</p>
                        <a href="/broker/cases/create" class="inline-block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Submit New Case
                        </a>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Recent Activity</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-600">No recent activity to display.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection