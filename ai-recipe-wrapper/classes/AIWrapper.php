<?php

class
AIWrapper
{
    private
        $ingredients
        = [];
    private
        $response
        =
        '';

    public
    function
    __construct
    ()
    {
// Controleer of config beschikbaar is
        if
        (!
        defined
        (
            'API_KEY'
        )) {
            require_once
                __DIR__
                .
                '/../config/config.php';
        }
    }

    public
    function
    processInput
    (
        $ingredients
    )
    {
        if
        (
            empty
            (
            $ingredients
            )) {
            throw
            new
            Exception
            (
                "Geen ingrediënten opgegeven"
            );
        }
        $this
            ->ingredients =
            $ingredients;

        // OpenAI API-aanroep
        $prompt = $this->buildPrompt($ingredients);
        $response = $this->callOpenAI($prompt);
        $this
            ->response =
            $response;
        return
            true;
    }

    private function buildPrompt($ingredients)
    {
        $ingredientList = implode(", ", $ingredients);
        return "Genereer een volledig recept in het Nederlands op basis van de volgende ingrediënten: $ingredientList. Geef het resultaat als gestructureerde JSON met de velden: naam, ingrediënten (lijst), bereidingstijd, stappen (lijst), moeilijkheidsgraad.";
    }

    private function callOpenAI($prompt)
    {
        $apiKey = API_KEY;
        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Je bent een behulpzame AI chef.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // Debug: log the prompt before API call
        file_put_contents(__DIR__ . '/../debug_openai_prompt.txt', $prompt);
        $result = curl_exec($ch);
        // Debug: log the raw response immediately after API call
        file_put_contents(__DIR__ . '/../debug_openai_response_raw.json', $result);
        if (curl_errno($ch)) {
            file_put_contents(__DIR__ . '/../debug_openai_error.txt', 'Curl error: ' . curl_error($ch));
            throw new Exception('Fout bij OpenAI API: ' . curl_error($ch));
        }
        curl_close($ch);
        $json = json_decode($result, true);
        // Debug: log the parsed JSON
        file_put_contents(__DIR__ . '/../debug_openai_response_parsed.json', print_r($json, true));
        if (!isset($json['choices'][0]['message']['content'])) {
            file_put_contents(__DIR__ . '/../debug_openai_error.txt', 'Missing expected content in response: ' . $result);
            throw new Exception('Ongeldig antwoord van OpenAI API');
        }
        return $json['choices'][0]['message']['content'];
    }

    public
    function
    getResponse
    ()
    {
        return
            $this
                ->response;
    }
}

