<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan daftar user
    public function index(Request $request)
    {
        $query = User::query();
        
        // Fitur Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(10); // Gunakan pagination

        return view('users.index', compact('users'));
    }

    // Tampilkan Form Tambah User
    public function create()
    {
        return view('users.create');
    }

    // Proses Simpan User
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // Tambahkan |confirmed jika ada input confirm password
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Tampilkan Detail User (Opsional)
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    // Tampilkan Form Edit User
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Proses Update User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id, // Ignore current user email
            'password' => 'nullable|string|min:8', // Password nullable saat edit
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Mencegah hapus diri sendiri
        if (auth()->id() == $id) {
            return redirect()->route('users.index')->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}