<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class ReplicateService
{
    protected $baseUrl = 'https://api.replicate.com/v1';

    /**
     * CREATE PREDICTION
     */
   public function generate($prompt)
{
    $response = Http::withToken(
        config('services.replicate.token')
    )->post(
        "{$this->baseUrl}/predictions",
        [
            'version' => 'openai/gpt-4o-mini',

            'input' => [
                'prompt' => $prompt,
                'system_prompt' => 'You are an expert ecommerce AI product comparison engine that returns only valid JSON.',
                'max_tokens' => 1500,
                'temperature' => 0.5,
            ]
        ]
    );

    $data = $response->json();

    if (!isset($data['id'])) {

        throw new \Exception(
            json_encode($data)
        );
    }

    return $data['id'];
}

    /**
     * WAIT FOR FINAL RESULT
     */
    public function waitForResult($id)
    {
        $maxAttempts = 60;

        for ($i = 0; $i < $maxAttempts; $i++) {

            $response = Http::withToken(
                config('services.replicate.token')
            )->get(
                "{$this->baseUrl}/predictions/{$id}"
            );

            $data = $response->json();

            // SUCCESS
            if (($data['status'] ?? null) === 'succeeded') {

                return $this->extractOutput($data);
            }

            // FAILED
            if (($data['status'] ?? null) === 'failed') {

                throw new \Exception(
                    $data['error'] ?? 'Prediction failed'
                );
            }

            // WAIT
            sleep(2);
        }

        throw new \Exception('AI response timeout');
    }

    /**
     * EXTRACT CLEAN OUTPUT
     */
    private function extractOutput($data)
{
    $output = $data['output'] ?? null;

    if (!$output) {
        throw new \Exception('No AI output returned');
    }

    if (is_array($output)) {
        $output = implode('', $output);
    }

    $output = trim($output);

    // 🔥 REMOVE markdown code blocks
    $output = preg_replace('/```json|```/', '', $output);

    // 🔥 EXTRACT ONLY JSON PART
    $start = strpos($output, '{');
    $end = strrpos($output, '}');

    if ($start === false || $end === false) {
        throw new \Exception('No JSON found in AI output');
    }

    $json = substr($output, $start, $end - $start + 1);

    $decoded = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception('JSON Parse Error: ' . json_last_error_msg());
    }

    return $decoded;
}

}