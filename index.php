<?php
require 'bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>AI-Powered Paint</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="toolbarContainer"></div>
    <canvas id="paintCanvas"></canvas>
    <div id="aiInteraction" style="display:none;">
        <input type="text" id="userInput" placeholder="Can I help you?">
        <button onclick="sendInputToAI()">Submit</button>
    </div>
    <?php if (!empty($existing_plugins)): ?>
        <div id="plugins">
            <h2>Plugins</h2>
        <?php foreach ($existing_plugins as $plugin): ?>
            <button data-plugin="<?= $plugin['name'] ?>"><?= $plugin['name'] ?></button>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script src="script.js"></script>
</body>
</html>

