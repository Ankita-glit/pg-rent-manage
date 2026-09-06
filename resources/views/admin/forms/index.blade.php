@extends('layouts.app')

@section('title', 'Renter Forms & Submissions - UrbanStay PG')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Renter Forms & Notice Center</h1>
            <p class="text-sm text-slate-400">Publish agreements, police verification forms, or notices for renters to fill out & submit</p>
        </div>

        <button onclick="toggleModal('createFormModal')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
            <i data-lucide="file-plus" class="w-4 h-4"></i>
            <span>Publish New Form to Renters</span>
        </button>
    </div>

    <!-- Active Published Forms Grid -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Published Active Forms</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($forms as $form)
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">Active Renter Form</span>
                        <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" onsubmit="return confirmCustomDelete(event, 'Remove form: {{ $form->title }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <h4 class="text-lg font-bold text-white">{{ $form->title }}</h4>
                    <p class="text-xs text-slate-300 leading-relaxed font-sans">{{ $form->description }}</p>
                </div>

                <div class="pt-3 border-t border-slate-800 space-y-3">
                    @if($form->template_file_path)
                    @php
                        $isPdf = Str::endsWith(strtolower($form->template_file_path), '.pdf');
                        $fileUrl = asset('storage/' . $form->template_file_path);
                    @endphp
                    <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-semibold flex items-center space-x-1">
                                <i data-lucide="{{ $isPdf ? 'file-text' : 'image' }}" class="w-3.5 h-3.5 text-pink-400"></i>
                                <span>Published {{ $isPdf ? 'PDF Document' : 'Image Form Template' }}</span>
                            </span>
                            <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-blue-400 hover:underline">Download</a>
                        </div>
                        
                        @if(!$isPdf)
                        <div class="relative group cursor-pointer overflow-hidden rounded-xl bg-black/40 border border-slate-800 h-36 flex items-center justify-center" onclick="openImageModal('{{ $fileUrl }}', '{{ $form->title }}')">
                            <img src="{{ $fileUrl }}" alt="{{ $form->title }}" class="max-h-full max-w-full object-contain">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>Click to Preview Image</span>
                            </div>
                        </div>
                        @else
                        <div class="p-3 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-between">
                            <span class="text-xs font-bold text-pink-300">📄 PDF Form Template Attached</span>
                            <button type="button" onclick="openImageModal('{{ $fileUrl }}', '{{ $form->title }}')" class="px-3 py-1 rounded-lg bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs shadow">
                                Preview PDF
                            </button>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/60 text-xs text-slate-500 italic">
                        No template file attached
                    </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Total Submissions: <strong class="text-white font-bold">{{ $form->submissions->count() }}</strong></span>
                        <span class="text-[11px] text-slate-500">Published {{ $form->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full glass-panel p-8 text-center text-slate-500 rounded-3xl">
                No active forms published yet. Click "Publish New Form to Renters" to create one.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Renter Form Submissions List -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Renter Form Submissions</h3>
            <span class="text-xs text-slate-400">Review renter filled responses and attached files</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Renter & Room</th>
                        <th class="p-4">Form Title</th>
                        <th class="p-4">Response Notes / File</th>
                        <th class="p-4">Submitted Date</th>
                        <th class="p-4">Review Status</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($submissions as $sub)
                    <tr class="hover:bg-slate-800/30">
                        <td class="p-4">
                            <div class="font-bold text-white">{{ $sub->renter->name ?? 'Unknown' }}</div>
                            <div class="text-[11px] text-slate-400">Room {{ $sub->renter->room->room_number ?? 'N/A' }}</div>
                        </td>

                        <td class="p-4 font-semibold text-indigo-300">
                            {{ $sub->form->title ?? 'N/A' }}
                        </td>

                        <td class="p-4 space-y-1">
                            @if($sub->response_notes)
                            <p class="text-slate-200 max-w-xs truncate" title="{{ $sub->response_notes }}">{{ $sub->response_notes }}</p>
                            @endif
                            @if($sub->submitted_file_path)
                            <button type="button" onclick="openImageModal('{{ asset('storage/' . $sub->submitted_file_path) }}', '{{ $sub->renter->name }} - Submission File')" class="text-blue-400 hover:underline font-semibold flex items-center space-x-1">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Preview Renter Document (PDF/Image)</span>
                            </button>
                            @endif
                        </td>

                        <td class="p-4 text-slate-400">
                            {{ $sub->created_at->format('d M Y, h:i A') }}
                        </td>

                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $sub->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : ($sub->status === 'submitted' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                                {{ $sub->status }}
                            </span>
                        </td>

                        <td class="p-4 text-right">
                            <button onclick="openReviewSubmissionModal({{ json_encode($sub) }})" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">
                                Review / Decision
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No renter form submissions received yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

<!-- Modal: Create Form -->
<div id="createFormModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-lg w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Publish New Form / Notice to Renters</h3>
            <button onclick="toggleModal('createFormModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Form Title *</label>
                <input type="text" name="title" required placeholder="e.g. Police Verification & Tenant Information Form 2026" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Form Instructions / Notice Description *</label>
                <textarea name="description" required placeholder="Explain what information or details renters need to fill out & submit..." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-24"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload Form / Notice File Template (PDF or Image File)</label>
                <input type="file" name="template_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
                <p class="text-[11px] text-slate-500 mt-1">Accepts PDF documents or Image files (JPG, PNG, WEBP) published by Admin.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Due Date (Optional)</label>
                <input type="date" name="due_date" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Publish Form to All Renters</button>
        </form>
    </div>
</div>

<!-- Modal: Review Submission -->
<div id="reviewSubmissionModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Review Renter Submission</h3>
            <button onclick="toggleModal('reviewSubmissionModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="reviewSubmissionForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Decision Status *</label>
                <select name="status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="approved">Approve Submission</option>
                    <option value="rejected">Reject Submission</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Admin Feedback Notes</label>
                <textarea name="admin_feedback" placeholder="e.g. Verified address proof document." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-24"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Save Review</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openReviewSubmissionModal(sub) {
        document.getElementById('reviewSubmissionForm').action = '/admin/forms/submissions/' + sub.id + '/review';
        toggleModal('reviewSubmissionModal');
    }
</script>
@endsection
