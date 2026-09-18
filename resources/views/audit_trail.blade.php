@extends('layouts.app')

<!-- If your top layout bar title (currently saying "Dashboard") uses a section, update it here -->
@section('title', 'System Audit Trail')

@section('content')
<div class="px-6 py-6">
    
    <!-- Main Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-white">System Audit Trail</h2>
    </div>
    
    <!-- Dark Table Container -->
    <div class="bg-[#1c1d21] border border-gray-800 rounded-xl overflow-x-auto shadow-sm">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-800 text-gray-400 text-xs uppercase tracking-wider">
                    <th class="py-4 px-6 font-medium">Date & Time</th>
                    <th class="py-4 px-6 font-medium">User</th>
                    <th class="py-4 px-6 font-medium">Action</th>
                    <th class="py-4 px-6 font-medium">Module</th>
                    <th class="py-4 px-6 font-medium">Details</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-300">
                @forelse($audits as $audit)
                <tr class="border-b border-gray-800 hover:bg-white/5 transition-colors">
                    <td class="py-4 px-6 text-gray-300 whitespace-nowrap">
                        {{ $audit->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="py-4 px-6 font-bold text-white">
                        {{ $audit->user ? $audit->user->name : 'System' }}
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold capitalize 
                            {{ $audit->event === 'created' ? 'text-green-500' : ($audit->event === 'deleted' ? 'text-red-500' : 'text-blue-500') }}">
                            {{ $audit->event }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        {{ class_basename($audit->auditable_type) }}
                    </td>
                    <td class="py-4 px-6">
                        @if($audit->event === 'updated')
                            <details class="cursor-pointer group">
                                <summary class="text-blue-400 hover:text-blue-300 font-medium outline-none">View Changes</summary>
                                <div class="mt-3 text-xs bg-[#121315] border border-gray-800 p-3 rounded-lg">
                                    <p class="font-bold text-gray-500 mb-1 uppercase tracking-wider text-[10px]">Before:</p>
                                    <code class="text-red-400 block mb-3 break-all">{{ json_encode($audit->old_values) }}</code>
                                    
                                    <p class="font-bold text-gray-500 mb-1 uppercase tracking-wider text-[10px]">After:</p>
                                    <code class="text-green-400 block break-all">{{ json_encode($audit->new_values) }}</code>
                                </div>
                            </details>
                        @else
                            <span class="text-gray-600">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 px-6 text-center text-gray-500">
                        No audit logs found. Try adding or editing a product first!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Links -->
    <div class="mt-6">
        {{ $audits->links() }}
    </div>
</div>
@endsection