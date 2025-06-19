<?php
// Inclusief de AIWrapper klasse
require_once 'classes/AIWrapper.php';
require_once 'classes/recipeformatter.php';
require_once 'classes/recipe.php';

// Controleer of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingredients'])) {
    try {
        // Valideer en verwerk de ingrediënten
        $ingredientsInput = trim($_POST['ingredients']);
        if (empty($ingredientsInput)) {
            throw new Exception("Geen ingrediënten opgegeven");
        }

        // Splits de ingrediënten op komma's en verwijder witruimte
        $ingredients = array_map('trim', explode(',', $ingredientsInput));

        // Maak een nieuwe instantie van de AIWrapper
        $wrapper = new AIWrapper();

        // Verwerk de ingrediënten
        $wrapper->processInput($ingredients);

        // Haal het antwoord op
        $response = $wrapper->getResponse();

        // Probeer het antwoord te parsen als recept
        $formatter = new RecipeFormatter();
        $recipe = $formatter->tryExtractRecipe($response);
        if ($recipe) {
            // Zet het recept in de sessie en redirect
            session_start();
            $_SESSION['recipe'] = serialize($recipe);
            header('Location: index.php');
            exit;
        } else {
            // Geen geldig recept gevonden
            header('Location: index.php?message=' . urlencode('Kon geen geldig recept genereren.')); 
            exit;
        }
    } catch (Exception $e) {
        // Stuur terug naar index met foutmelding
        header('Location: index.php?message=Fout: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Als het formulier niet correct is verzonden
    header('Location: index.php?message=Ongeldig verzoek');
    exit;
}
?>
