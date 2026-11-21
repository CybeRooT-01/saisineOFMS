<?php

namespace App\Http\Controllers;

use App\Models\RequestType;
use App\Models\MandatoryField;
use App\Models\Role;
use App\Models\User;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display mandatory fields management page.
     */
    public function mandatoryFields()
    {
        $requestTypes = RequestType::with('mandatoryFields')->get();
        
        return view('admin.mandatory-fields', compact('requestTypes'));
    }

    /**
     * Store a new mandatory field.
     */
    public function storeMandatoryField(Request $request)
    {
        $validated = $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,email,number,date,textarea',
            'field_label' => 'required|string|max:255',
            'validation_rules' => 'nullable|string',
        ]);

        MandatoryField::create($validated);

        return redirect()->route('admin.mandatory-fields')
            ->with('success', 'Champ obligatoire ajouté avec succès');
    }

    /**
     * Delete a mandatory field.
     */
    public function destroyMandatoryField(MandatoryField $mandatoryField)
    {
        $mandatoryField->delete();

        return redirect()->route('admin.mandatory-fields')
            ->with('success', 'Champ obligatoire supprimé avec succès');
    }

    /**
     * Display roles management page.
     */
    public function roles()
    {
        $roles = Role::withCount('users')->get();
        
        return view('admin.roles', compact('roles'));
    }

    /**
     * Store a new role.
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle créé avec succès');
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle mis à jour avec succès');
    }

    /**
     * Delete a role.
     */
    public function destroyRole(Role $role)
    {
        // Check if role has users
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('admin.roles')
                ->with('error', "Impossible de supprimer ce rôle car {$usersCount} utilisateur(s) l'utilisent actuellement");
        }

        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle supprimé avec succès');
    }

    /**
     * Display users management page.
     */
    public function users()
    {
        $users = User::with(['roles', 'structure'])->get();
        $roles = Role::all();
        $structures = Structure::orderBy('fullpath')->get();
        
        return view('admin.users', compact('users', 'roles', 'structures'));
    }

    /**
     * Store a new user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'email' => 'required|email|unique:users,email',
            'structure_id' => 'nullable|exists:structures,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'login' => $validated['login'],
            'email' => $validated['email'],
            'structure_id' => $validated['structure_id'] ?? null,
            'password' => Hash::make('password'), // Default password
        ]);

        $user->roles()->attach($validated['roles']);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur créé avec succès (mot de passe par défaut: password)');
    }

    /**
     * Update an existing user.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'structure_id' => 'nullable|exists:structures,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'login' => $validated['login'],
            'email' => $validated['email'],
            'structure_id' => $validated['structure_id'] ?? null,
        ]);

        $user->roles()->sync($validated['roles']);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Delete a user.
     */
    public function destroyUser(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Display structures management page.
     */
    public function structures()
    {
        $structures = Structure::with(['parent', 'children'])->withCount('users')->orderBy('fullpath')->get();
        $parentStructures = Structure::whereNull('parent_structure_id')->get();
        
        return view('admin.structures', compact('structures', 'parentStructures'));
    }

    /**
     * Store a new structure.
     */
    public function storeStructure(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_structure_id' => 'nullable|exists:structures,id',
        ]);

        Structure::create($validated);

        return redirect()->route('admin.structures')
            ->with('success', 'Structure créée avec succès');
    }

    /**
     * Update an existing structure.
     */
    public function updateStructure(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_structure_id' => 'nullable|exists:structures,id',
        ]);

        // Prevent circular reference
        if ($validated['parent_structure_id'] == $structure->id) {
            return redirect()->route('admin.structures')
                ->with('error', 'Une structure ne peut pas être son propre parent');
        }

        // Check if new parent is not a child of current structure
        if ($validated['parent_structure_id']) {
            $parent = Structure::find($validated['parent_structure_id']);
            while ($parent) {
                if ($parent->id == $structure->id) {
                    return redirect()->route('admin.structures')
                        ->with('error', 'Impossible de créer une référence circulaire');
                }
                $parent = $parent->parent;
            }
        }

        $structure->update($validated);

        return redirect()->route('admin.structures')
            ->with('success', 'Structure mise à jour avec succès');
    }

    /**
     * Delete a structure.
     */
    public function destroyStructure(Structure $structure)
    {
        // Check if structure has users
        $usersCount = $structure->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('admin.structures')
                ->with('error', "Impossible de supprimer cette structure car {$usersCount} utilisateur(s) y sont rattachés");
        }

        // Check if structure has children
        $childrenCount = $structure->children()->count();
        if ($childrenCount > 0) {
            return redirect()->route('admin.structures')
                ->with('error', "Impossible de supprimer cette structure car elle est la structure parente de {$childrenCount} sous-structure(s)");
        }

        $structure->delete();

        return redirect()->route('admin.structures')
            ->with('success', 'Structure supprimée avec succès');
    }
}
