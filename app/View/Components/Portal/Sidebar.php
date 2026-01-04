<?php

namespace App\View\Components\Portal;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Sidebar extends Component
{
    public $menuGroups;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->menuGroups = [];
            return;
        }

        // Get all user roles
        $roles = $user->roles;
        
        // Find the first role that has a corresponding menu configuration
        $selectedRoleKey = null;
        
        // Check if roles are loaded/exist
        if ($roles && $roles->count() > 0) {
            foreach ($roles as $role) {
                $roleKey = Str::snake($role->name);
                if (config()->has("portal_sidebar.menu.{$roleKey}")) {
                    $selectedRoleKey = $roleKey;
                    break;
                }
            }
        } else {
            // Fallback for users with no roles (e.g. freshly registered)
            // potentially default to 'student' or 'applicant' if desired, 
            // but if no role is assigned, maybe empty is correct.
            // For this specific issue, we suspect 'student' might be intended.
             $selectedRoleKey = 'student'; 
        }

        // Retrieve menu from config
        if ($selectedRoleKey && config()->has("portal_sidebar.menu.{$selectedRoleKey}")) {
            $this->menuGroups = config("portal_sidebar.menu.{$selectedRoleKey}", []);
        } else {
            $this->menuGroups = [];
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.portal.sidebar');
    }
}
