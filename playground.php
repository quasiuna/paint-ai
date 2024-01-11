<?php
use quasiuna\paintai\AiScript;
use quasiuna\paintai\RateLimiter;

require 'bootstrap.php';

$user = new RateLimiter;
$script = new AiScript(['name' => 'playground', 'user' => $user->getUserIdentifier()]);
$prompt = $script->getPrompt();

?>
<!doctype html>
<html>
    <head>
        <title>Paint AI Playground</title>
        <style>
:root {
    --radius: 8px;
}
* {margin:0;padding:0;box-sizing: border-box;}
label {margin-bottom:5px;color:#74ae98}
label,input,textarea {display:block;width:100%}
input,textarea {background:#222;color:#ddd;font-size:16px;padding:5px}
body {font-size:16px;color:#ccc;background:#222;padding:15px;font-family:monospace}
header {margin:15px 30px}
h1,h2,h3,h4 {margin-bottom:15px}
input[type=submit] {font-size:20px;padding:7px 30px;background:#244a3b;border-radius:var(--radius)}
.container {border-radius: var(--radius)}
.container > div {flex-grow: 1;background:#444;padding:15px;margin:15px;border-radius:var(--radius)}
.mb-1 {margin-bottom:15px}

#i {width: 25%}
#o {width: 75%; height: 80vh}
#output {overflow:auto;padding:15px;background:#222}
        </style>
    </head>
    <body>
        <header>
            <h1>Playground</h1>
        </header>
        <div class="container" style="display:flex;justify-content:space-between">
            <div id="i">
                <h2>Input</h2>
                <div class="mb-1">
                    <label>User Tool Name</label>
                    <input type="text" name="name" value="Pencil">
                </div>
                <div class="mb-1">
                    <label>User Tool description</label>
                    <textarea rows="4" name="description">A simple pencil that draws a simple line</textarea>
                </div>
                <div class="mb-1">
                    <label>Prompt</label>
                    <textarea rows="6" name="prompt"><?= $prompt ?></textarea>
                </div>
                <div>
                    <input type="submit">
                </div>
            </div>
            <div id="o">
                <h2 id="heading_output">Output <span></span></h2>
                <div id="output"></div>
            </div>
        </div>
    <script>

(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const submitButton = document.querySelector('input[type="submit"]');
        const outputDiv = document.getElementById('output');
        outputDiv.style.height = '90%'; // Example height
        outputDiv.style.overflowY = 'auto';

        const loadingIndicator = document.createElement('span');
        loadingIndicator.textContent = 'Loading...';
        loadingIndicator.style.display = 'none';

        submitButton.addEventListener('click', (event) => {
            event.preventDefault();

            // Gather form data
            const name = document.querySelector('input[name="name"]').value;
            const description = document.querySelector('textarea[name="description"]').value;
            const prompt = document.querySelector('textarea[name="prompt"]').value;

            const data = JSON.stringify({ name, description, prompt });

            // Show loading indicator
            const outputHeader = document.querySelector('#heading_output span');
            outputHeader.appendChild(loadingIndicator);
            loadingIndicator.style.display = 'inline';

            // Fetch API for AJAX request
            fetch('http://localhost:6006/code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: data
            })
            .then(response => response.text())
            .then((responseText) => {
                setTimeout(() => {
                    loadingIndicator.style.display = 'none';
                }, 300);

                // Stream the request and response
                const dateTime = new Date().toISOString().replace('T', ' ').substring(0, 19);
                const requestString = `<div class=mb-1><strong>Request (at ${dateTime}):</strong><pre>${data}</pre></div><hr class=mb-1>`;
                const responseString = `<div class=mb-1><strong>Response (at ${dateTime}):</strong><pre>${responseText}</pre></div><hr class=mb-1>`;

                streamText(requestString + responseString);
            })
            .catch((error) => {
                console.error('Error:', error);
                setTimeout(() => {
                    loadingIndicator.style.display = 'none';
                }, 300);
            });
        });

        function streamText(text) {
            outputDiv.querySelectorAll("div").forEach(el => {
                el.style.color = '#777';
            });
            outputDiv.innerHTML += text.replace(/\\n/g, "<br>").replace(/\\\//g, "/");
            outputDiv.scrollTo({ top: outputDiv.scrollHeight, behavior: 'smooth' });
        }
    });
})();

    </script>
    </body>
</html>
