<?php
namespace App\Http\Controllers;
use App\Models\{User, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        $users = User::with('stores')->where('id', '!=', auth()->id())->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $stores = Store::where('is_active', true)->get();
        return view('admin.users.create', compact('stores'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'role' => 'required|in:admin,kepala_toko,karyawan',
            'pin' => 'required|digits:4',
            'phone' => 'nullable|string',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'is_active' => 'boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'pin' => Hash::make($request->pin),
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->role !== 'admin' && $request->store_ids) {
            $user->stores()->attach($request->store_ids);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user) {
        $stores = Store::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'stores'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'role' => 'required|in:admin,kepala_toko,karyawan',
            'phone' => 'nullable|string',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'is_active' => 'boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->role !== 'admin') {
            $user->stores()->sync($request->store_ids ?? []);
        } else {
            $user->stores()->sync([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user) {
        $user->update(['is_active' => false]);
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dinonaktifkan.');
    }
}