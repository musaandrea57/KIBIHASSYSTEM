<?php

namespace App\Policies;

use App\Models\EvaluationPeriod;
use App\Models\User;

class EvaluationPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('open_close_evaluations');
    }

    public function view(User $user, EvaluationPeriod $evaluationPeriod): bool
    {
        return $user->hasPermissionTo('open_close_evaluations');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('open_close_evaluations');
    }

    public function update(User $user, EvaluationPeriod $evaluationPeriod): bool
    {
        return $user->hasPermissionTo('open_close_evaluations');
    }

    public function delete(User $user, EvaluationPeriod $evaluationPeriod): bool
    {
        return $user->hasPermissionTo('open_close_evaluations');
    }
}
