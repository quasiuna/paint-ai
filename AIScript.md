# AI Script

This project provides a way to get code written by an AI that slots straight in to an existing app.

The workflow is as follows:

1. Human user requests a new feature
2. Feature developed by AI
3. Code validated and tested automatically
4. Working code deployed live
5. Human user requests modifications to the feature
6. AI makes changes
7. Code validated and tested automatically
8. Working code deployed live

The required materials are:

- Prompt
  - Requirements
  - Programming Language
- AI
  - Provider
  - Model
- Plugin
  - Validation
  - Deployment


## Project Structure

REST API

POST /code - Create a new piece of code
  - Parameters
    - Provider - the AI back-end
    - Template - the base prompt
    - Instruction - specific user instructions
    - Plugin - the rules for validation and deployment of code

POST /code/{id} - Change a piece of code
GET /code - List all pieces of code created
GET /code/{id} - Show an individual piece of code
