<?php
use quasiuna\paintai\AiScript;

require 'bootstrap.php';

$script = new AiScript(['name' => 'playground']);
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
body {font-size:16px;color:#ccc;background:#222;padding:30px;font-family:monospace}
header {margin:30px}
h1,h2,h3,h4 {margin-bottom:15px}
input[type=submit] {font-size:20px;padding:7px 30px;background:#244a3b;border-radius:var(--radius)}
.container {background:#333;margin:30px;border-radius: var(--radius)}
.container > div {flex-grow: 1;max-width:45%;background:#444;padding:15px;margin:15px;border-radius:var(--radius)}
.mb-1 {margin-bottom:15px}

#output {overflow:auto;padding:15px;background:#222}
        </style>
    </head>
    <body>
        <header>
            <h1>Playground</h1>
        </header>
        <div class="container" style="display:flex;justify-content:space-between">
            <div>
                <h2>Input</h2>
                <div class="mb-1">
                    <label>User Tool Name</label>
                    <input type="text" name="name">
                </div>
                <div class="mb-1">
                    <label>User Tool description</label>
                    <textarea rows="4" name="description"></textarea>
                </div>
                <div class="mb-1">
                    <label>Prompt</label>
                    <textarea rows="6" name="prompt"><?= $prompt ?></textarea>
                </div>
                <div>
                    <input type="submit">
                </div>
            </div>
            <div>
                <h2>Output</h2>
                <div id="output"></div>
            </div>
        </div>
    <script>
(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const submitButton = document.querySelector('input[type="submit"]');
        const outputDiv = document.getElementById('output');
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
            const outputHeader = document.querySelector('h2').nextElementSibling;
            outputHeader.appendChild(loadingIndicator);
            loadingIndicator.style.display = 'inline';

            // Fetch API for AJAX request
            fetch('http://localhost:6006/server.php?method=ai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: data
            })
            .then(response => response.text())
            .then((responseText) => {
                // Hide loading indicator
                loadingIndicator.style.display = 'none';

                // Append request and response
                const dateTime = new Date().toISOString().replace('T', ' ').substring(0, 19);
                outputDiv.innerHTML += `<div><strong>Request (at ${dateTime}):</strong><pre>${data}</pre></div>`;
                outputDiv.innerHTML += '<hr>';
                outputDiv.innerHTML += `<div><strong>Response (at ${dateTime}):</strong><pre>${responseText}</pre></div>`;
                outputDiv.innerHTML += '<hr>';
            })
            .catch((error) => {
                console.error('Error:', error);
                loadingIndicator.style.display = 'none';
            });
        });
    });
})();

    </script>
    </body>
</html>
