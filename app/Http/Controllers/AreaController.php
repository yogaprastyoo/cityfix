<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(): View
    {
        $areas = Area::withCount('reports')->orderBy('name')->paginate(15);

        return view('areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('areas.form', ['area' => new Area(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Area::create($this->validated($request));

        return redirect()->route('areas.index')->with('success', 'Area berhasil ditambahkan.');
    }

    public function edit(Area $area): View
    {
        return view('areas.form', compact('area'));
    }

    public function update(Request $request, Area $area): RedirectResponse
    {
        $area->update($this->validated($request));

        return redirect()->route('areas.index')->with('success', 'Area berhasil diperbarui.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        if ($area->reports()->exists()) {
            return back()->withErrors(['area' => 'Area sudah memiliki laporan. Nonaktifkan saja, jangan dihapus.']);
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Area berhasil dihapus.');
    }

    /**
     * @return array{name: string, code: ?string, is_active: bool}
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        return [...$validated, 'is_active' => $request->boolean('is_active')];
    }
}
