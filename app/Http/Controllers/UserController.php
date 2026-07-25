<?php
namespace App\Http\Controllers;
use App\Models\{User, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditService;

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
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|alpha_dash',
            'role' => 'required|in:admin,kepala_toko,karyawan',
            'pin' => 'required|digits:4',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'pin' => Hash::make($request->pin),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user = User::create($data);

        if ($request->role !== 'admin' && $request->store_ids) {
            $user->stores()->attach($request->store_ids);
        }

        AuditService::log('create_user', 'Pengguna ' . $user->name . ' (' . $user->role . ') ditambahkan.', 'User', $user->id);
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user) {
        $user->load(['stores', 'salesReports']);
        $approvedCount = $user->salesReports()->where('status', 'disetujui')->count();
        $rejectedCount = $user->salesReports()->where('status', 'ditolak')->count();
        $pendingCount = $user->salesReports()->where('status', 'diproses')->count();
        $totalApprovedAmount = $user->salesReports()->where('status', 'disetujui')->sum('total_amount');
        return view('admin.users.show', compact('user', 'approvedCount', 'rejectedCount', 'pendingCount', 'totalApprovedAmount'));
    }

    public function edit(User $user) {
        $stores = Store::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'stores'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'role' => 'required|in:admin,kepala_toko,karyawan',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($data);

        if ($request->role !== 'admin') {
            $user->stores()->sync($request->store_ids ?? []);
        } else {
            $user->stores()->sync([]);
        }

        AuditService::log('update_user', 'Pengguna ' . $user->name . ' diperbarui.', 'User', $user->id);
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user) {
        $user->update(['is_active' => false]);
        AuditService::log('deactivate_user', 'Pengguna ' . $user->name . ' dinonaktifkan.', 'User', $user->id);
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dinonaktifkan.');
    }

    public function resetPin(Request $request, User $user) {
        $request->validate(['new_pin' => 'required|digits:4']);
        $user->update(['pin' => Hash::make($request->new_pin)]);
        AuditService::log('reset_pin', 'PIN pengguna ' . $user->name . ' di-reset oleh admin.', 'User', $user->id);
        return back()->with('success', 'PIN ' . $user->name . ' berhasil di-reset.');
    }
}
