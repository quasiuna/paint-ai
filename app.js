const express = require('express');
const app = express();
const http = require('http').Server(app);
const OpenAI = require('openai');

console.log(process.env);

const openai = new OpenAI({
    apiKey: "ABCD"
});

http.listen(3000, function(){
    console.log('listening on *:3000');
});

app.post('/process_input', async (req, res) => {
    const userInput = req.body.input;

    try {
        const response = await openai.createCompletion({
            model: "text-davinci-003", // or the latest model
            prompt: `Create a JavaScript plugin for a paint program based on the following user request: "${userInput}"`,
            max_tokens: 150
        });

        // Assume the response contains the JavaScript code for the plugin
        const pluginCode = response.choices[0].text;

        res.json({ pluginCode });
    } catch (error) {
        console.error(error);
        res.status(500).send('Error processing request');
    }
});

