<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageManagementController extends Controller
{
    public function index(): View
    {
        $packages = Package::withCount('bookings')->orderBy('id', 'asc')->get();
        $addOns = AddOn::withCount('bookingAddOns')->orderBy('id', 'asc')->get();

        return view('admin.packages.index', compact('packages', 'addOns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:5', 'max:120'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'prints_included' => ['nullable', 'string', 'max:100'],
            'operational_hours' => ['nullable', 'string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Package::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? ($validated['name'].' Photobox'),
            'duration' => $validated['duration'] ?? 15,
            'max_participants' => $validated['max_participants'] ?? 4,
            'price' => $validated['price'],
            'prints_included' => $validated['prints_included'] ?: '2 Strip + Soft File',
            'operational_hours' => $validated['operational_hours'] ?: 'Buka 09.00 — 23.00 WIB',
            'badge' => $validated['badge'] ?? null,
            'category' => $validated['category'] ?? 'BOOTH',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Paket foto {$validated['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:5', 'max:120'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'prints_included' => ['nullable', 'string', 'max:100'],
            'operational_hours' => ['nullable', 'string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $package->description ?? ($validated['name'].' Photobox'),
            'duration' => $validated['duration'] ?? $package->duration ?? 15,
            'max_participants' => $validated['max_participants'] ?? 4,
            'price' => $validated['price'],
            'prints_included' => $validated['prints_included'] ?: '2 Strip + Soft File',
            'operational_hours' => $validated['operational_hours'] ?: 'Buka 09.00 — 23.00 WIB',
            'badge' => $validated['badge'] ?? null,
            'category' => $validated['category'] ?? 'BOOTH',
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $package->is_active,
        ]);

        return back()->with('success', "Paket foto {$package->name} berhasil diperbarui.");
    }

    public function toggleActive(Package $package): RedirectResponse
    {
        $package->is_active = ! $package->is_active;
        $package->save();

        $status = $package->is_active ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';

        return back()->with('success', "Paket foto {$package->name} berhasil {$status}.");
    }

    public function destroy(Package $package): RedirectResponse
    {
        if ($package->bookings()->count() > 0) {
            $package->update(['is_active' => false]);

            return back()->with('info', "Paket {$package->name} memiliki riwayat transaksi, status diubah menjadi non-aktif alih-alih dihapus permanen.");
        }

        $package->delete();

        return back()->with('success', "Paket foto {$package->name} berhasil dihapus permanen.");
    }

    // Add-ons sub-management
    public function storeAddOn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        AddOn::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Layanan tambahan (Add-On) {$validated['name']} berhasil ditambahkan.");
    }

    public function updateAddOn(Request $request, AddOn $addOn): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $addOn->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Layanan tambahan {$addOn->name} berhasil diperbarui.");
    }

    public function toggleAddOn(AddOn $addOn): RedirectResponse
    {
        $addOn->is_active = ! $addOn->is_active;
        $addOn->save();

        $status = $addOn->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Layanan tambahan {$addOn->name} berhasil {$status}.");
    }

    public function destroyAddOn(AddOn $addOn): RedirectResponse
    {
        $addOn->delete();

        return back()->with('success', 'Layanan tambahan berhasil dihapus.');
    }
}
