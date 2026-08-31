<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class ReplicateService
{
    protected string $baseUrl = 'https://api.replicate.com/v1';

    /**
     * Create a new AI prediction.
     */
    public function generate(string $prompt): string
    {
        $token = config('services.replicate.token');

        if (empty($token)) {
            throw new \Exception(
                'Replicate API token is not configured.'
            );
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(30)
            ->post(
                "{$this->baseUrl}/predictions",
                [
                    'version' => 'openai/gpt-4o-mini',

                    'input' => [
                        'prompt' => $prompt,

                        'system_prompt' =>
                            'You are SmartCart AI, an ecommerce product comparison assistant. Use only the supplied catalog data. Never invent product facts or unsupported performance claims. Return only professional Markdown.',

                        'max_tokens' => 1500,

                        'temperature' => 0.2,
                    ],
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Validate API response
        |--------------------------------------------------------------------------
        */

        if (!$response->successful()) {
            throw new \Exception(
                "Replicate request failed with status {$response->status()}: "
                . $response->body()
            );
        }

        $data = $response->json();

        $predictionId = $data['id'] ?? null;

        if (!$predictionId) {
            throw new \Exception(
                'Replicate did not return a prediction ID.'
            );
        }

        return $predictionId;
    }

    /**
     * Wait for the prediction to finish.
     */
    public function waitForResult(string $id): string
    {
        $token = config('services.replicate.token');

        if (empty($token)) {
            throw new \Exception(
                'Replicate API token is not configured.'
            );
        }

        $maxAttempts = 60;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {

            /*
            |--------------------------------------------------------------------------
            | Wait before polling
            |--------------------------------------------------------------------------
            */

            sleep(2);

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(30)
                ->get(
                    "{$this->baseUrl}/predictions/{$id}"
                );

            /*
            |--------------------------------------------------------------------------
            | Validate polling response
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {
                throw new \Exception(
                    "Replicate polling failed with status {$response->status()}: "
                    . $response->body()
                );
            }

            $data = $response->json();

            $status = $data['status'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Prediction completed
            |--------------------------------------------------------------------------
            */

            if ($status === 'succeeded') {
                return $this->extractOutput($data);
            }

            /*
            |--------------------------------------------------------------------------
            | Prediction failed
            |--------------------------------------------------------------------------
            */

            if ($status === 'failed') {
                throw new \Exception(
                    $data['error'] ?? 'Replicate prediction failed.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Prediction canceled
            |--------------------------------------------------------------------------
            */

            if ($status === 'canceled') {
                throw new \Exception(
                    'Replicate prediction was canceled.'
                );
            }
        }

        throw new \Exception(
            'AI comparison timed out while waiting for Replicate.'
        );
    }

    /**
     * Extract the final text returned by Replicate.
     */
    private function extractOutput(array $data): string
    {
        $output = $data['output'] ?? null;

        if (empty($output)) {
            throw new \Exception(
                'Replicate completed successfully but returned no AI output.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Replicate may return streamed text chunks
        |--------------------------------------------------------------------------
        */

        if (is_array($output)) {
            $output = implode('', $output);
        }

        $output = trim((string) $output);

        if ($output === '') {
            throw new \Exception(
                'Replicate returned an empty AI comparison.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Defensive Markdown cleanup
        |--------------------------------------------------------------------------
        |
        | The prompt requests raw Markdown, but remove code fences if the model
        | adds them anyway.
        |
        */

        $output = preg_replace(
            '/^```(?:markdown|md)?\s*|\s*```$/i',
            '',
            $output
        );

        return trim($output);
    }
}