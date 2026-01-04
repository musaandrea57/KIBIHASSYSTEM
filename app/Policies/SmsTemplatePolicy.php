<?php

namespace App\Policies;

use App\Models\SmsTemplate;
use App\Models\User;

class SmsTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_sms_settings') || $user->hasPermissionTo('send_bulk_sms');
    }

    public function view(User $user, SmsTemplate $smsTemplate): bool
    {
        return $user->hasPermissionTo('manage_sms_settings') || $user->hasPermissionTo('send_bulk_sms');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_sms_settings');
    }

    public function update(User $user, SmsTemplate $smsTemplate): bool
    {
        return $user->hasPermissionTo('manage_sms_settings');
    }

    public function delete(User $user, SmsTemplate $smsTemplate): bool
    {
        return $user->hasPermissionTo('manage_sms_settings');
    }
}
