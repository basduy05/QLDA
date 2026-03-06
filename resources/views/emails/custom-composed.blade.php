@component('emails.layout', [
    'subject' => $emailSubject,
    'emailTitle' => $emailSubject,
])
    <div style="font-size:14px; color:#334155; line-height:1.8;">
        {!! $emailBody !!}
    </div>
    <div style="margin-top:24px; padding-top:16px; border-top:1px solid #f1f5f9;">
        <p style="margin:0; font-size:13px; color:#64748b;">
            — Aperlex
        </p>
    </div>
@endcomponent
