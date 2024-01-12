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
            <div id="headerNav">
                <a id="headerLogo" href="/"><img src="/img/logo.png"></a>
                <span>(Optimal on large screens)</span>
                <a id="headerStartOver" href="/">Clear Drawing</a>
                <a id="headerExportImage" href="#" onclick="document.getElementById('exportButton').click(); return false;">Export Image</a>
                <a id="headerAbout" href="#">About</a>
                <a id="headerContact" href="#">Contact</a>
            </div>
            <div id="headerControls">
                <button id="exportButton" class="btn-wide mr-3" title="Save current image as a file"><i class="fa fa-save"></i> Save Image</button>
                <div id="animationControls">
                    <span>animation</span>
                    <button id="pause" title="Play/Pause Animations"><i class="fa fa-pause"></i></button>
                    <input id="animSpeed" title="Animation Speed" type="range" min="1" max="10">
                </div>
            </div>
        </div>
    
        <div id="sidebar">
            <button class="p-2 mb-4" id="newPlugin"><i class="fa fa-plus-circle"></i> New Tool</button>
            <h2>Tools</h2>
            <div id="tools"></div>
            <div id="custom"></div>
            <div id="features">
                <button class="p-2 mb-4 secondary" id="improvePlugin"><i class="fa fa-edit"></i> <span></span></button>

                <?php if (!empty($existing_plugins)): ?>
                <div id="plugins">
                    <h2>Plugins</h2>
                    <div class="scrollable">
                    <?php foreach ($existing_plugins as $plugin): ?>
                        <div class="existing-plugin mb-0">
                            <button class="m-0 secondary" data-plugin="<?= $plugin['name'] ?>"><?= $plugin['name'] ?></button>
                            <button class="m-0 secondary button-delete" data-delete="<?= $plugin['name'] ?>"><i class="fa fa-trash"></i></button>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <button class="p-2" id="loadPlugins"><i class="fa fa-arrow-circle-up"></i> Load all</button>
                </div>
                <?php else: ?>
                    <div id="start">
                        <div class="bounce" style="font-size: 24px"><i class="fa fa-arrow-circle-up"></i></div>
                        <div>Start here</div>
                    </div>
                <?php endif; ?>
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
        </div>

        <div id="overlay"></div>
        <div class="modal" id="exportModal"></div>
        <div class="modal p-0" id="aiInteraction">
            <form>
            <div class="p-3 bg-1">
                <h2 class="c-2 m-0 center">Use AI to create a new tool</h2>
            </div>
            <div class="p-4">
                <div id="aiInteractionForm">
                    <p class="c-1">How would you like to paint? Describe it...then an AI  will try to build it.</p>
                    <div>
                        <label>Describe your new painting tool</label>
                        <textarea rows="3" id="tool_description" placeholder="My tool should..." required></textarea>
                    </div>
                    <div>
                        <label>Give it a name</label>
                        <input type="text" id="tool_name" placeholder="Example Name" required>
                    </div>
                </div>
                <div id="newPluginProgressContainer" class="progress-container mb-3"></div>
                <div id="newPluginContainer" class="mb-3">
                    <button class="btn-lg" id="createPlugin">Submit</button>
                    <div id="createPluginStatus"></div>
                </div>
                <p>Be patient...it can take up to one minute for the AI to write the code for your tool!</p>
            </div>
            </form>
        </div>
        <div class="modal p-0" id="aiImprove">
            <form>
            <div class="p-3 bg-1">
                <h2 class="c-2 m-0 center">Use AI to improve this tool</h2>
            </div>
            <div class="p-4">
                <div id="aiImproveForm">
                    <input type="hidden" id="edit_tool_name" value="">
                    <div>
                        <label id="edit_tool_description_label">Describe how the AI should change <span></span></label>
                        <textarea rows="3" id="edit_tool_description" required></textarea>
                    </div>
                </div>
                <div id="editPluginProgressContainer" class="progress-container mb-3"></div>
                <div id="editPluginContainer" class="mb-3">
                    <button class="btn-lg" id="editPlugin">Submit</button>
                    <div id="editPluginStatus"></div>
                </div>
                <p>Be patient...it can take up to one minute for the AI to edit the code for your tool!</p>
            </div>
            </form>
        </div>
        <div class="modal" id="newToolSuccess">
            <div style="color: var(--primary-color); font-size: 40px;display:flex; flex-direction: column; align-items: center">
                <i class="fa fa-check-circle"></i>
                <span>Success!</span>
                <span style="font-size: 16px">A brand new tool has been coded for you. Try it out!</span>
            </div>
        </div>
        <div class="modal p-0" id="about">
            <div class="bg-2 p-4" style="display:flex;justify-content: space-between; align-items:center">
                <h1 class="p-0 m-0">About Paint AI</h1>
                <img src="/img/logo.png" style="height:40px">
            </div>
            <div class="p-4">
                <p><b>Paint AI</b> is a fun project exploring how AI can be used to develop software.</p>
                <p>Thousands of software developers are using AI chat and code-completion tools to help them write code.
                In a world-first, <b>Paint AI</b> takes this one step further, getting AI to write, test and deploy code without any human involvement!</p>
                <p><b>How to use Paint AI</b></p>
                <p>Paint AI is the world's first paint program with no pens, paintbrushes or anything to paint with. It is completely useless
                until you ask the AI to create some painting tools, just for you.</p>
                <p>Click <b>New Tool</b> and describe what you would like. The AI will attempt to write some software for you that
                will give you what you need. Another AI will test the code and deploy it live.</p>
            </div>
            <div class="p-4">
                <p>Source code at <a href="https://github.com/quasiuna/paint-ai">https://github.com/quasiuna/paint-ai</a></p>
            </div>
        </div>
        <div class="modal p-0" id="contact">
            <div class="bg-2 p-4" style="display:flex;justify-content: space-between; align-items:center">
                <h1 class="p-0 m-0">Contact Paint AI</h1>
                <img src="/img/logo.png" style="height:40px">
            </div>
            <div class="p-4">
                <p>Paint AI is a fun project brought to you by Group Mind. <a target="_blank" href="https://www.groupmind.co.uk/pages/contact">Contact us</a> here.</p>

            </div>
        </div>
    </div>
    <script src="/js/main.js"></script>
    <script src="/js/Canvas.js"></script>
    <script src="/js/Export.js"></script>
    <script src="/js/Plugins.js"></script>
    <script src="/js/Tool.js"></script>
    <script src="/js/Animator.js"></script>
    <script src="/js/Test.js"></script>
</body>
</html>
