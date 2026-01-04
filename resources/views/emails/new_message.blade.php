<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; margin-bottom: 20px; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; color: white; }
        .badge-urgent { background-color: #dc3545; }
        .badge-normal { background-color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Official Communication</h2>
            <p>From: <strong>{{ $senderName }}</strong></p>
        </div>

        <p>You have received a new official message via the KIBIHAS Academic Management System.</p>

        <div style="background-color: #f1f1f1; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <p><strong>Subject:</strong> {{ $subject }}</p>
            <p><strong>Classification:</strong> {{ $classification }}</p>
            @if($priority === 'urgent' || $priority === 'high')
                <p><strong>Priority:</strong> <span class="badge badge-urgent">{{ ucfirst($priority) }}</span></p>
            @endif
        </div>

        <p>Please log in to the system to view the full details and any attachments.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('login') }}" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Login to KIBIHAS</a>
        </div>

        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} KIBIHAS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
