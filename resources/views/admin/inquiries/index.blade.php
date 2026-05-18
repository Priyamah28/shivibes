@extends('admin.layout')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Corporate Inquiries</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500">
                <thead class="bg-slate-50 text-xs uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Name / Company</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Message</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $inquiry->name }}</div>
                                <div class="text-xs text-slate-500">{{ $inquiry->company_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $inquiry->email }}</div>
                                <div class="text-xs text-slate-500">{{ $inquiry->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate" title="{{ $inquiry->message }}">
                                    {{ $inquiry->message }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $inquiry->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex min-w-[110px] items-center justify-center whitespace-nowrap rounded-full px-3 py-1 text-center text-xs font-semibold
                                    @if($inquiry->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($inquiry->status === 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-emerald-100 text-emerald-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.inquiries.update_status', $inquiry) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded border-slate-300 py-1 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $inquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $inquiry->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                    <button type="submit" class="rounded bg-slate-800 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-700">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                No inquiries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquiries->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
@endsection
