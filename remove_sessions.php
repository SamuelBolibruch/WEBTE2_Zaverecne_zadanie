<?php
session_start(); // Spustenie relácie

// Zrušenie všetkých premenných relácie
session_unset();

// Úplné zrušenie relácie
session_destroy();

echo "Relácia bola úspešne zrušená.";
?>
