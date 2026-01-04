<?php

namespace App\Policies;

use App\Models\EvaluationTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EvaluationTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_evaluation_templates');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return $user->hasPermissionTo('manage_evaluation_templates');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_evaluation_templates');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return $user->hasPermissionTo('manage_evaluation_templates');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return $user->hasPermissionTo('manage_evaluation_templates');
    }
}
