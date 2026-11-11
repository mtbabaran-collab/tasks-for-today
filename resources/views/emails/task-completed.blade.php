{{-- Blade email view for App\Mail\TaskCompleted. Inline styles because email apps ignore CSS files. --}}
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Task completed</title></head>
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e5e7eb;">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 8px;font-size:13px;color:#059669;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;">Tasks for Today</p>
                <h1 style="margin:0 0 16px;font-size:22px;">✅ Task completed</h1>
                <p style="margin:0 0 16px;font-size:15px;line-height:1.5;">
                    The task <strong>{{ $task->title }}</strong> was marked as <strong>done</strong>{{ $completedBy ? ' by '.$completedBy : '' }}.
                </p>
                <table role="presentation" cellspacing="0" cellpadding="6" style="font-size:14px;margin-bottom:20px;">
                    <tr><td style="color:#6b7280;">Priority</td><td>{{ $task->priorityLabel() }}</td></tr>
                    <tr><td style="color:#6b7280;">Due date</td><td>{{ $task->due_date?->format('M j, Y') ?? 'None' }}</td></tr>
                    <tr><td style="color:#6b7280;">Completed</td><td>{{ $task->completed_at?->format('M j, Y g:i A') }}</td></tr>
                </table>
                @if ($task->description)
                    <p style="margin:0 0 20px;font-size:14px;color:#374151;line-height:1.5;">{{ $task->description }}</p>
                @endif
                <a href="{{ route('tasks.show', $task) }}" style="display:inline-block;background:#059669;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:6px;font-weight:bold;font-size:14px;">View the task</a>
            </td>
        </tr>
    </table>
    <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:16px;">Sent automatically because the task's status changed to Done.</p>
</body>
</html>
