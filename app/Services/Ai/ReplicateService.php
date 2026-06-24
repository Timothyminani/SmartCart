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
                'system_prompt' => 'You are an expert ecommerce AI product comparison engine that returns only professional markdown.',
                'max_tokens' => 1500,
                'temperature' => 0.5,
            ]
        ]
    );

    $data = $response->json();

   
logger('REPLICATE RESPONSE', $response->json());



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
    /**

* EXTRACT CLEAN OUTPUT
  */

private function extractOutput($data)
{
    // DEBUG THE FULL RESPONSE
    logger('FULL REPLICATE RESPONSE', $data);

    $output = $data['output'] ?? null;

    if (!$output) {

        // Sometimes output may not exist directly
        if (isset($data['prediction']['output'])) {
            $output = $data['prediction']['output'];
        }
    }

    if (!$output) {
        throw new \Exception('No AI output returned');
    }

    // Convert array chunks to string
    if (is_array($output)) {
        $output = implode('', $output);
    }

    $output = trim($output);

    // Remove accidental code fences
    $output = str_replace([
        '```markdown',
        '```md',
        '```'
    ], '', $output);

    return trim($output);
}



}