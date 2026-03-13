<?php
// start_learning_ai.php

$venvPath = __DIR__ . '/venv/Scripts/activate'; // Windows
$pythonPath = __DIR__ . '/venv/Scripts/python.exe'; // Windows

// Start learning_ai.py in background
pclose(popen("start /B $pythonPath " . __DIR__ . "/learning_ai.py", "r"));

echo "Learning AI server started";
?>