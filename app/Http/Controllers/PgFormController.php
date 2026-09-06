<?php

namespace App\Http\Controllers;

use App\Models\PgForm;
use App\Models\RenterFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PgFormController extends Controller
{
    public function adminIndex()
    {
        $forms = PgForm::with(['submissions.renter'])->latest()->get();
        $submissions = RenterFormSubmission::with(['form', 'renter.room.floor'])->latest()->paginate(15);

        return view('admin.forms.index', compact('forms', 'submissions'));
    }

    public function storeForm(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'template_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'due_date' => 'nullable|date',
        ]);

        $filePath = null;
        if ($request->hasFile('template_file')) {
            $filePath = $request->file('template_file')->store('admin_forms', 'public');
        }

        PgForm::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'template_file_path' => $filePath,
            'is_active' => true,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return back()->with('success', 'Form published to all renters successfully!');
    }

    public function destroyForm(PgForm $form)
    {
        if ($form->template_file_path) {
            Storage::disk('public')->delete($form->template_file_path);
        }
        $form->delete();
        return back()->with('success', 'Form removed successfully.');
    }

    public function reviewSubmission(Request $request, RenterFormSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'admin_feedback' => $validated['admin_feedback'] ?? null,
        ]);

        return back()->with('success', 'Form submission review saved!');
    }

    public function renterSubmitForm(Request $request, PgForm $form)
    {
        $validated = $request->validate([
            'response_notes' => 'nullable|string|max:2000',
            'submitted_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
        ]);

        if (!$request->filled('response_notes') && !$request->hasFile('submitted_file')) {
            return back()->with('error', 'Please provide either response details or attach a completed document.');
        }

        $filePath = null;
        if ($request->hasFile('submitted_file')) {
            $filePath = $request->file('submitted_file')->store('renter_submissions', 'public');
        }

        RenterFormSubmission::updateOrCreate(
            [
                'pg_form_id' => $form->id,
                'renter_id' => Auth::id(),
            ],
            [
                'response_notes' => $validated['response_notes'] ?? null,
                'submitted_file_path' => $filePath ?? ($form->submissionForUser(Auth::id())->submitted_file_path ?? null),
                'status' => 'submitted',
                'admin_feedback' => null,
            ]
        );

        return back()->with('success', 'Your form submission was received successfully!');
    }
}
