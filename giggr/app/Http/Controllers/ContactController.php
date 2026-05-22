<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'in:general,partnership,feature,bug,other'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'rgpd' => ['required', 'accepted'],
        ]);

        Mail::to(config('mail.contact_recipient'))->send(new ContactMessageReceived(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            subjectKey: $data['subject'],
            body: $data['message'],
        ));

        return redirect()
            ->route('contact')
            ->with('contact_success', true);
    }
}
