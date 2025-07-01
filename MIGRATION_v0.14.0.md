# Migration to OpenAI Client v0.14.0

This document outlines the changes made to upgrade the Flarum extension to be compatible with OpenAI PHP client v0.14.0.

## Key Changes Made

### 1. Updated Dependencies
- Updated `composer.json` to use `"openai-php/client": "^0.14.0"`

### 2. API Migration
- **Completions API → Chat Completions API**: Migrated from the deprecated `completions()` endpoint to the new `chat()->completions()` endpoint
- **Prompt Format**: Changed from string prompts to structured message arrays with roles
- **Model Update**: Default model changed from `text-davinci-003` to `gpt-3.5-turbo`

### 3. Code Changes

#### OpenAIClient.php
- Migrated `$client->completions()->create()` to `$client->chat()->completions()->create()`
- Updated prompt format to use messages array:
  ```php
  'messages' => [
      [
          'role' => 'user',
          'content' => $prompt,
      ],
  ]
  ```
- Response handling updated to access `$response['choices'][0]['message']['content']`

#### Response Structure Changes
- **Old**: `$result->choices[0]->text`
- **New**: `$response['choices'][0]['message']['content']`

### 4. Service Registration
- Added proper dependency injection for `OpenAIClient` in `extend.php`
- Updated default model setting to `gpt-3.5-turbo`

### 5. Breaking Changes
- The `completions()` method now returns an array instead of a string
- All code consuming the API responses needed to be updated to handle the new structure
- Model names and capabilities changed with the move to Chat Completions API

## What You Need to Do

1. **Update Dependencies**: Run `composer update` to install the new OpenAI client version
2. **Clear Cache**: Clear any cached model data as the API structure has changed
3. **Test Functionality**: Verify that ChatGPT responses are working correctly with the new API

## Benefits of the Migration

- **Future-Proof**: Uses the current OpenAI API that's actively maintained
- **Better Models**: Access to newer, more capable models like GPT-3.5 Turbo and GPT-4
- **Improved Performance**: Chat Completions API is optimized for conversational use cases
- **Enhanced Features**: Better support for context and multi-turn conversations

## Troubleshooting

If you encounter issues:

1. Ensure your OpenAI API key is still valid
2. Check that the new model (`gpt-3.5-turbo`) is available in your OpenAI account
3. Verify that your API usage limits haven't been exceeded
4. Clear the extension cache if model listing fails