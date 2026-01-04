<?php

namespace App\Http\Controllers\Admin\Communication;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SmsTemplateController extends Controller
{
    public function index()
    {
        Gate::authorize('send_bulk_sms'); // Allow senders to see templates too? Or separate manage permission?
        // Requirement: "SMS Templates (Admin) CRUD templates with preview"
        // Let's use manage_sms_settings or a new permission.
        // User said: "manage_sms_settings (admin)"
        Gate::authorize('manage_sms_settings');

        $templates = SmsTemplate::orderBy('created_at', 'desc')->get();
        return view('admin.communication.sms.templates.index', compact('templates'));
    }

    public function create()
    {
        Gate::authorize('manage_sms_settings');
        return view('admin.communication.sms.templates.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_sms_settings');
        $request->validate([
            'key' => 'required|string|unique:sms_templates,key|max:50',
            'message_body' => 'required|string',
        ]);

        SmsTemplate::create([
            'key' => $request->key,
            'message_body' => $request->message_body,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.communication.sms.templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(SmsTemplate $template)
    {
        Gate::authorize('manage_sms_settings');
        return view('admin.communication.sms.templates.edit', compact('template'));
    }

    public function update(Request $request, SmsTemplate $template)
    {
        Gate::authorize('manage_sms_settings');
        $request->validate([
            'key' => 'required|string|max:50|unique:sms_templates,key,' . $template->id,
            'message_body' => 'required|string',
        ]);

        $template->update([
            'key' => $request->key,
            'message_body' => $request->message_body,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.communication.sms.templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(SmsTemplate $template)
    {
        Gate::authorize('manage_sms_settings');
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }
}
