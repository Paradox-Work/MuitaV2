@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📊 Analyst Dashboard</h1>
                <p class="text-gray-600 mb-6">Risk analysis and reporting</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3">Risk Statistics</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-red-600 font-medium">High Risk Cases</span>
                                <span class="font-bold">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-600 font-medium">Medium Risk Cases</span>
                                <span class="font-bold">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-600 font-medium">Low Risk Cases</span>
                                <span class="font-bold">0</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3">Reports</h3>
                        <div class="space-y-2">
                            <a href="/analyst/reports/daily" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                📅 Daily Inspection Report
                            </a>
                            <a href="/analyst/reports/monthly" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                📈 Monthly Analytics
                            </a>
                            <a href="/analyst/reports/risk" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                ⚠️ Risk Assessment Summary
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Data Analysis</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-600">No data available for analysis. Cases need to be processed first.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection