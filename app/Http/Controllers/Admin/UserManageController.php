<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Add this line
use DB;

class UserManageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() 
    {
        $data['users'] = User::all();
        return view('admin.users.index', $data);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,spg', // Assuming roles are user or admin
        ]);

        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
            'role' => $request->role,
        ]);

        // Redirect back with a success message
        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $data['user'] = User::findOrFail($id);
        $data['provinsi'] = DB::table('provinsi')->get();

        return view('admin.users.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'password' => 'nullable|min:8|confirmed', // Only validate password if provided
            'kota' => 'required',
            'provinsi' => 'required',
            'text_provinsi' => 'required|string',
            'text_kota' => 'required|string',
        ]);

        // dd($request->all());

        // Retrieve the user to update
        $user = User::findOrFail($id);
        
        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->kota = $request->kota;
        $user->provinsi = $request->provinsi;
        $user->text_provinsi = $request->text_provinsi;
        $user->text_kota = $request->text_kota;

        // Update password only if a new password is provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Use a transaction to save changes
        try {
            DB::beginTransaction();
            
            $user->save();
            
            DB::commit();
            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        }

        return redirect()->route('admin.users')->with('error', 'User not found.');
    }

    public function getCities($prov_id)
    {
        $cities = DB::table('kabupaten')
                    ->where('prov_id', $prov_id)
                    ->get();

        return response()->json($cities); // Return cities as JSON
    }
}
