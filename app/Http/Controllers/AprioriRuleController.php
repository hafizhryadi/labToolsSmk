<?php

namespace App\Http\Controllers;

use App\Models\AprioriRule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AprioriRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rules = AprioriRule::query()->latest()->paginate(15)->withQueryString();

        return $request->expectsJson()
            ? response()->json($rules)
            : inertia('apriori-rules/index', ['rules' => $rules]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return $request->expectsJson()
            ? response()->json(['rule' => null])
            : inertia('apriori-rules/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $rule = AprioriRule::create($request->validate([
            'antecedent' => ['required', 'array'],
            'consequent' => ['required', 'array'],
            'support' => ['required', 'numeric', 'between:0,100'],
            'confidence' => ['required', 'numeric', 'between:0,100'],
            'lift' => ['nullable', 'numeric', 'min:0'],
        ]));

        return $request->expectsJson()
            ? response()->json($rule, 201)
            : to_route('apriori-rules.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, AprioriRule $aprioriRule)
    {
        return $request->expectsJson()
            ? response()->json($aprioriRule)
            : inertia('apriori-rules/show', ['rule' => $aprioriRule]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, AprioriRule $aprioriRule)
    {
        return $request->expectsJson()
            ? response()->json($aprioriRule)
            : inertia('apriori-rules/edit', ['rule' => $aprioriRule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AprioriRule $aprioriRule): Response
    {
        $aprioriRule->update($request->validate([
            'antecedent' => ['required', 'array'],
            'consequent' => ['required', 'array'],
            'support' => ['required', 'numeric', 'between:0,100'],
            'confidence' => ['required', 'numeric', 'between:0,100'],
            'lift' => ['nullable', 'numeric', 'min:0'],
        ]));

        return $request->expectsJson()
            ? response()->json($aprioriRule->fresh())
            : to_route('apriori-rules.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, AprioriRule $aprioriRule): Response
    {
        $aprioriRule->delete();

        return $request->expectsJson()
            ? response()->json(status: 204)
            : to_route('apriori-rules.index');
    }
}
