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
            <a href="#">View</a>
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
            <div id="footerColors">
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
            <div id="footerText"><span>&copy; Paint AI.</span> <a href="#">Contact Us</a></div>
        </div>

        <div id="overlay"></div>
        <div class="modal" id="exportModal"></div>
        <div class="modal" id="aiInteraction">
            <h2>Create a new tool with AI</h2>
            <p>How do you want to paint? With a felt tip pen? Or with a

                <span class="ideas">
                    <span class="idea">Neon stamp that only contain words from the opening lines of Quentin Tarantino movies?.</span>
                    <span class="idea">Bubble Brush that paints bubbles that pop to reveal random famous paintings in miniature?</span>
                    <span class="idea">Time-Travel Crayon that sketches a scene that ages or reverts to a past era in real-time?</span>
                    <span class="idea">Mood Ring Brush that changes color and texture based on your current mood, detected through your device's sensors?</span>
                    <span class="idea">Dreamscape Doodler that draws elements from your last dream, interpreting them into surreal art?</span>
                    <span class="idea">Constellation Connector that creates starry night skies where the stars connect to form mythical creatures and zodiac signs?</span>
                    <span class="idea">Echo Painter that replicates the sound waves of your favorite song into visual patterns and colors?</span>
                    <span class="idea">Gravity-Defying Ink brush that draws shapes and lines that float and bobble on the canvas as if in zero gravity?</span>
                    <span class="idea">Historic Pen that automatically styles your drawing in the manner of a randomly selected historical art movement?</span>
                    <span class="idea">Invisible Ink that creates drawings that are only visible under certain virtual 'lights' or angles?</span>
                    <span class="idea">Nature Brush that paints with textures and colors sourced from the environment you're in, using your device's camera?</span>
                    <span class="idea">Seasonal Stroke that automatically changes your artwork’s theme and palette to match the current season?</span>
                    <span class="idea">Shadow Puppet Pencil that draws figures that animate like shadow puppets, mimicking your movements?</span>
                    <span class="idea">Telepathic Pencil that draws concepts or objects you're thinking about, interpreting your thoughts into images?</span>
                    <span class="idea">Underwater Watercolor that paints strokes that ripple and flow as if underwater, complete with marine life swimming through?</span>
                    <span class="idea">Weather Brush that integrates real-time weather data into your artwork, like raindrops or snowflakes falling across the canvas?</span>
                    <span class="idea">Kaleidoscope Spray that sprays colors that continuously morph into symmetrical, kaleidoscopic patterns?</span>
                    <span class="idea">Mythical Marker that sketches transform into creatures and elements from various myths and legends?</span>
                    <span class="idea">Aurora Borealis Brush that paints with the shimmering, shifting colors of the Northern Lights?</span>
                    <span class="idea">Whispering Wind that creates art that moves and changes subtly as if blown by a gentle breeze?</span>
                    <span class="idea">Retro Television Tool that gives your artwork a vintage TV screen effect, complete with static and scan lines?</span>
                </span>
                
                It's up to you! Dream it and AI will (try to) build it.</p>
            <div>
                <label>Give your new tool a name</label>
                <input type="text" id="tool" placeholder="Example">
            </div>
            <div>
                <label>Describe it</label>
                <textarea rows="3" id="prompt" placeholder="What would you like your tool to do?"></textarea>
            </div>
            <button onclick="sendInputToAI()">Submit</button>
        </div>
    </div>
    <script src="/js/main.js"></script>
    <script src="/js/Canvas.js"></script>
    <script src="/js/Export.js"></script>
    <script src="/js/Plugins.js"></script>
    <script src="/js/Tool.js"></script>
</body>
</html>