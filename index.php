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
    <div id="main" class="container">
        <div id="toolbarContainer"></div>
        <canvas id="paintCanvas"></canvas>

        <div>
            <button id="newPlugin">New Plugin</button>
        </div>

        <div id="aiInteraction" style="display:none;">
            <div>
                <label>Tool Name</label>
                <input type="text" id="tool" placeholder="ExampleTool">
            </div>
            <div>
                <label>Description</label>
                <textarea rows="3" id="prompt" placeholder="What would you like your tool to do?"></textarea>
            </div>
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
    </div>
    <script src="script.js"></script>
</body>
</html>

