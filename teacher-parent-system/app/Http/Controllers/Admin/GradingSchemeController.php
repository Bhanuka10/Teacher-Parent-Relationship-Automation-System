<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGradingBandRequest;
use App\Http\Requests\Admin\UpdateGradingBandRequest;
use App\Models\GradingSchemeBand;

class GradingSchemeController extends Controller
{
    public function index()
    {
        $bands = GradingSchemeBand::orderBy('position')->get();

        return view('admin.grading-scheme.index', compact('bands'));
    }

    public function store(StoreGradingBandRequest $request)
    {
        $validated = $request->validated();
        $nextPosition = (int) GradingSchemeBand::max('position') + 1;

        GradingSchemeBand::create([
            'min_mark' => $validated['min_mark'],
            'max_mark' => $validated['max_mark'],
            'grade' => $validated['grade'],
            'is_passing' => $request->boolean('is_passing'),
            'position' => $nextPosition,
        ]);

        return redirect()->route('admin.grading-scheme.index')->with('success', 'Grading band added.');
    }

    public function update(UpdateGradingBandRequest $request, GradingSchemeBand $gradingSchemeBand)
    {
        $validated = $request->validated();

        $gradingSchemeBand->update([
            'min_mark' => $validated['min_mark'],
            'max_mark' => $validated['max_mark'],
            'grade' => $validated['grade'],
            'is_passing' => $request->boolean('is_passing'),
        ]);

        return redirect()->route('admin.grading-scheme.index')->with('success', 'Grading band updated.');
    }

    public function destroy(GradingSchemeBand $gradingSchemeBand)
    {
        $gradingSchemeBand->delete();

        return redirect()->route('admin.grading-scheme.index')->with('success', 'Grading band removed.');
    }
}
