<?php
require_once 'classes/recipe.php';
session_start();
$recipe = null;
if (isset($_SESSION['recipe'])) {
    $recipe = unserialize($_SESSION['recipe']);
    unset($_SESSION['recipe']);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>AI Recept Generator</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1>AI Recept Generator</h1>
    <p>Voer hieronder je ingrediënten in en ontvang een recept!</p>

    <form action="process.php" method="POST">
      <div class="form-group">
        <label for="ingredients">Ingrediënten (gescheiden door komma's):</label>
        <textarea id="ingredients" name="ingredients" rows="4" required placeholder="bijv. ui, knoflook, tomaat, pasta"></textarea>
      </div>

      <button type="submit">Genereer Recept</button>
    </form>

    <?php if (isset($_GET['message'])): ?>
      <div class="message">
        <?php echo htmlspecialchars($_GET['message']); ?>
      </div>
    <?php endif; ?>

    <?php if ($recipe): ?>
      <div class="recipe-card">
        <h2><?= htmlspecialchars($recipe->naam) ?></h2>
        <p><strong>Bereidingstijd:</strong> <?= htmlspecialchars($recipe->bereidingstijd) ?></p>
        <p><strong>Moeilijkheidsgraad:</strong> <?= htmlspecialchars($recipe->moeilijkheidsgraad) ?></p>
        <h3>Ingrediënten</h3>
        <ul>
          <?php foreach ($recipe->ingrediënten as $ing): ?>
            <li><?= htmlspecialchars($ing) ?></li>
          <?php endforeach; ?>
        </ul>
        <h3>Stappen</h3>
        <ol>
          <?php foreach ($recipe->stappen as $step): ?>
            <li><?= htmlspecialchars($step) ?></li>
          <?php endforeach; ?>
        </ol>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
