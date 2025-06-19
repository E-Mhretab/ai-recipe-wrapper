<?php
// class RecipeFormatter
// {
//     public function formatRecipe(string $rawOutput): ?Recipe
//     {
//         try {
//             // Probeer de output te decoderen als JSON
//             $data = json_decode($rawOutput, true);

//             // Controleer of de benodigde velden aanwezig zijn
//             if (
//                 !$data ||
//                 !isset($data['naam']) ||
//                 !isset($data['ingrediënten']) ||
//                 !isset($data['bereidingstijd']) ||
//                 !isset($data['stappen']) ||
//                 !isset($data['moeilijkheidsgraad'])
//             ) {
//                 return null; // Geen geldige data
//             }

//             // Maak een nieuw Recipe object
//             return new Recipe(
//                 $data['naam'],
//                 $data['ingrediënten'],
//                 $data['bereidingstijd'],
//                 $data['stappen'],
//                 $data['moeilijkheidsgraad']
//             );
//         } catch (Exception $e) {
//             // Bij fouten, return null
//             return null;
//         }
//     }
// }

class RecipeFormatter
{
    // Probeer eerst de volledige JSON te parsen, anders minder streng extraheren
    public function formatRecipe(string $rawOutput): ?Recipe
    {
        try {
            // Probeer de output te decoderen als JSON
            $data = json_decode($rawOutput, true);

            // Controleer of de benodigde velden aanwezig zijn
            if (
                !$data ||
                !isset($data['naam']) ||
                !isset($data['ingrediënten']) ||
                !isset($data['bereidingstijd']) ||
                !isset($data['stappen']) ||
                !isset($data['moeilijkheidsgraad'])
            ) {
                return null; // Geen geldige data
            }

            // Maak een nieuw Recipe object
            return new Recipe(
                $data['naam'],
                $data['ingrediënten'],
                $data['bereidingstijd'],
                $data['stappen'],
                $data['moeilijkheidsgraad']
            );
        } catch (Exception $e) {
            // Bij fouten, return null
            return null;
        }
    }

    // Probeer eerst de volledige JSON te parsen, anders minder streng extraheren
    public function tryExtractRecipe(string $rawOutput): ?Recipe
    {
        // Eerst proberen als JSON te parsen
        $recipe = $this->formatRecipe($rawOutput);
        if ($recipe) {
            return $recipe;
        }

        // Als dat mislukt, proberen we een minder strenge methode
        // Bijvoorbeeld: reguliere expressies gebruiken om data te extraheren
        $naam = $this->extractName($rawOutput);
        $ingrediënten = $this->extractIngredients($rawOutput);
        // ... andere extracties kunnen hier toegevoegd worden

        if ($naam && !empty($ingrediënten)) {
            return new Recipe(
                $naam,
                $ingrediënten,
                "Onbekend",  // onbekende bereidingstijd
                [],          // geen stappen bekend
                "Onbekend"   // onbekende moeilijkheidsgraad
            );
        }

        return null;
    }

    // Hulpmethode voor naam-extractie via regex
    private function extractName(string $text): ?string
    {
        // Voorbeeld: pak eerste regel die begint met "Naam:" gevolgd door de naam
        if (preg_match('/Naam:\s*(.+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    // Hulpmethode voor ingrediënten-extractie via regex
    private function extractIngredients(string $text): array
    {
        // Voorbeeld: zoek een lijst na "Ingrediënten:" en splits op nieuwe regels of komma's
        if (preg_match('/Ingrediënten:\s*(.+)/is', $text, $matches)) {
            $ingredientsRaw = trim($matches[1]);
            // Splits bijvoorbeeld op nieuwe regels of komma's
            $ingredients = preg_split('/[\r\n,]+/', $ingredientsRaw);
            // Trim alle ingrediënten en filter lege strings eruit
            return array_filter(array_map('trim', $ingredients));
        }
        return [];
    }
}
