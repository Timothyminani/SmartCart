<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;

class IntentValidationService
{
    /**
     * VALIDATE AND NORMALIZE AI INTENT
     */
    public function validate(array $intent): array
    {
        return [
            'category' => $this->validateCategory($intent['category'] ?? null),

            'brand' => $this->validateBrand($intent['brand'] ?? null),

            'budget_max' => $this->validateBudget(
                $intent['budget_max'] ?? null
            ),

            'use_case' => $this->normalizeUseCase(
                $intent['use_case'] ?? null
            ),

            'min_specs' => $this->validateSpecs(
                $intent['min_specs'] ?? []
            ),

            'priority_features' => $this->normalizeFeatures(
                $intent['priority_features'] ?? []
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY VALIDATION (FIXED - MULTI MATCH SUPPORT)
    |--------------------------------------------------------------------------
    */
    private function validateCategory($category)
{
    if (!$category) {
        return null;
    }

    $category = strtolower(trim($category));

    // STEP 1: direct match
    $exact = Category::whereRaw('LOWER(name) = ?', [$category])->first();

    if ($exact) {
        return [
            'primary' => $exact->name,
            'alternatives' => []
        ];
    }

    // STEP 2: semantic mapping (🔥 IMPORTANT FIX)
    $map = [
        'phone' => 'Smartphones',
        'phones' => 'Smartphones',
        'smartphone' => 'Smartphones',
        'mobile' => 'Smartphones',

        'laptop' => 'Computers',
        'computer' => 'Computers',
        'pc' => 'Computers',

        'tablet' => 'Tablets',
    ];

    if (isset($map[$category])) {
        $dbCategory = Category::whereRaw('LOWER(name) = ?', [strtolower($map[$category])])->first();

        if ($dbCategory) {
            return [
                'primary' => $dbCategory->name,
                'alternatives' => []
            ];
        }
    }

    // STEP 3: fallback fuzzy search (SAFE)
    $matches = Category::whereRaw('LOWER(name) LIKE ?', ["%{$category}%"])
        ->limit(3)
        ->pluck('name')
        ->toArray();

    if (empty($matches)) {
        return null;
    }

    return [
        'primary' => $matches[0],
        'alternatives' => array_slice($matches, 1)
    ];
}
    /*
    |--------------------------------------------------------------------------
    | BRAND VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateBrand($brand)
    {
        if (!$brand) {
            return null;
        }

        $brand = strtolower(trim($brand));

        $exact = Brand::whereRaw('LOWER(name) = ?', [$brand])->first();

        if ($exact) {
            return $exact->name;
        }

        $fallback = Brand::whereRaw('LOWER(name) LIKE ?', ["%{$brand}%"])->first();

        return $fallback ? $fallback->name : null;
    }

    /*
    |--------------------------------------------------------------------------
    | BUDGET VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateBudget($budget)
    {
        if (!$budget) {
            return null;
        }

        $budget = preg_replace('/[^0-9]/', '', (string) $budget);
        $budget = (int) $budget;

        return $budget > 0 ? $budget : null;
    }

    /*
    |--------------------------------------------------------------------------
    | SPEC VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateSpecs(array $specs)
    {
        $clean = [];

        if (!empty($specs['ram'])) {
            $clean['ram'] = $this->normalizeRam($specs['ram']);
        }

        if (!empty($specs['storage'])) {
            $clean['storage'] = $this->normalizeStorage($specs['storage']);
        }

        return $clean;
    }

    /*
    |--------------------------------------------------------------------------
    | RAM NORMALIZATION
    |--------------------------------------------------------------------------
    */
    private function normalizeRam($ram)
    {
        preg_match('/(\d+)/', strtolower($ram), $matches);

        return isset($matches[1]) ? (int)$matches[1] . 'GB' : null;
    }

    /*
    |--------------------------------------------------------------------------
    | STORAGE NORMALIZATION
    |--------------------------------------------------------------------------
    */
    private function normalizeStorage($storage)
    {
        preg_match('/(\d+)\s?(gb|tb)/i', strtolower($storage), $matches);

        if (!$matches) {
            return null;
        }

        return strtoupper($matches[1] . $matches[2]);
    }

    /*
    |--------------------------------------------------------------------------
    | USE CASE NORMALIZATION (IMPROVED COVERAGE)
    |--------------------------------------------------------------------------
    */
    private function normalizeUseCase($useCase)
    {
        if (!$useCase) {
            return null;
        }

        $useCase = strtolower(trim($useCase));

        $map = [
            'video editing' => 'video_editing',
            'content creation' => 'content_creation',
            'creator work' => 'content_creation',

            'gaming' => 'gaming',

            'business' => 'business',
            'office work' => 'business',

            'student' => 'students',
            'students' => 'students',

            'travel' => 'travel',

            // 🔥 NEW IMPORTANT ADDITIONS
            'interior design' => '3d_rendering',
            'rendering' => '3d_rendering',
            '3d modeling' => '3d_rendering',
            'cad' => 'engineering',
            'architecture' => 'engineering',
        ];

        return $map[$useCase] ?? $useCase;
    }

    /*
    |--------------------------------------------------------------------------
    | FEATURES NORMALIZATION
    |--------------------------------------------------------------------------
    */
    private function normalizeFeatures(array $features)
    {
        return collect($features)
            ->map(fn($feature) => strtolower(trim($feature)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}