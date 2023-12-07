<?php
require 'bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paint AI - An app that is developed as you use it</title>
    <meta name="description" content="Paint with a twist - Use AI to add any drawing or painting tool you like while you create. This app's source code doesn't exist yet - it is written as you use it.">
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <link rel="stylesheet" href="/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <div id="header">
            <img src="/img/logo.png">
            <span>(Optimal on large screens)</span>
            <a href="#">File</a>
            <a href="#">Edit</a>
            <a href="#">View</span>
        </div>
    
        <div id="sidebar">
            <div id="tools"></div>
            <div id="custom"></div>
            <div id="features">
                <button id="newPlugin">New Tool</button>

                <?php if (!empty($existing_plugins)): ?>
                <div id="plugins">
                    <h2>Plugins</h2>
                    <div class="scrollable">
                    <?php foreach ($existing_plugins as $plugin): ?>
                        <div data-plugin="<?= $plugin['name'] ?>"><?= $plugin['name'] ?></div>
                    <?php endforeach; ?>
                    </div>
                    <button id="loadPlugins">Load</button>
                </div>
                <?php endif; ?>

                <button id="exportButton">Export</button>
            </div>
        </div>
    
        <div id="canvas">
            <canvas id="paintCanvas"></canvas>
        </div>
    
        <div id="footer">
            <div class="selected-color-button"></div>
            <div class="color-button" data-color="#000000"></div>
            <div class="color-button" data-color="#555555"></div>
            <div class="color-button" data-color="#AAAAAA"></div>
            <div class="color-button" data-color="#FFFFFF"></div>
            <div class="color-button" data-color="#E57373"></div> <!-- Red -->
            <div class="color-button" data-color="#F06292"></div> <!-- Pink -->
            <div class="color-button" data-color="#BA68C8"></div> <!-- Purple -->
            <div class="color-button" data-color="#9575CD"></div> <!-- Deep Purple -->
            <div class="color-button" data-color="#7986CB"></div> <!-- Indigo -->
            <div class="color-button" data-color="#64B5F6"></div> <!-- Blue -->
            <div class="color-button" data-color="#4FC3F7"></div> <!-- Light Blue -->
            <div class="color-button" data-color="#4DD0E1"></div> <!-- Cyan -->
            <div class="color-button" data-color="#4DB6AC"></div> <!-- Teal -->
            <div class="color-button" data-color="#81C784"></div> <!-- Green -->
            <div class="color-button" data-color="#AED581"></div> <!-- Light Green -->
            <div class="color-button" data-color="#DCE775"></div> <!-- Lime -->
            <div class="color-button" data-color="#FFF176"></div> <!-- Yellow -->
            <div class="color-button" data-color="#FFD54F"></div> <!-- Amber -->
            <div class="color-button" data-color="#FFB74D"></div> <!-- Orange -->
            <div class="color-button" data-color="#FF8A65"></div> <!-- Deep Orange -->
            <div class="color-button" data-color="#A1887F"></div> <!-- Brown -->
            <div class="color-button" data-color="#E0E0E0"></div> <!-- Grey -->
            <div class="color-button" data-color="#90A4AE"></div> <!-- Blue Grey -->
        </div>

        <div class="overlay" id="exportOverlay"></div>
        <div class="modal" id="exportModal"></div>
    </div>
    <script src="/js/main.js"></script>
    <script src="/js/Canvas.js"></script>
    <script src="/js/Export.js"></script>
    <script src="/js/Plugins.js"></script>
    <script src="/js/Tool.js"></script>
</body>
</html>