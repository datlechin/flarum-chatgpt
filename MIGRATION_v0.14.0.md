# Migration to OpenAI Client v0.14.0

This document outlines the changes made to upgrade the Flarum extension to be compatible with OpenAI PHP client v0.14.0.

## Key Changes Made

### 1. Updated Dependencies
- Updated `composer.json` to use `"openai-php/client": "^0.14.0"`

### 2. API Migration
- **Completions API → Chat Completions API**: Migrated from the deprecated `completions()` endpoint to the new `chat()->completions()` endpoint
- **Prompt Format**: Changed from string prompts to structured message arrays with roles
- **Model Update**: Default model changed from `text-davinci-003` to `gpt-3.5-turbo` (configurable via settings)

### 3. Code Changes

#### OpenAIClient.php
- Migrated `$client->completions()->create()` to `$client->chat()->completions()->create()`
- **Dynamic Model Selection**: Model is now read from settings instead of being hard-coded
- **Configurable Parameters**: `max_tokens` and `temperature` are now configurable via settings
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
- OpenAIClient now receives `SettingsRepositoryInterface` to read configuration
- Updated default model setting to `gpt-3.5-turbo`
- Added new setting: `datlechin-chatgpt.temperature` (default: 0.7)

### 5. Settings Configuration
The extension now supports the following configurable settings:

- `datlechin-chatgpt.model` - OpenAI model to use (default: `gpt-3.5-turbo`)
- `datlechin-chatgpt.max_tokens` - Maximum tokens for responses (default: 100)
- `datlechin-chatgpt.temperature` - Response creativity level (default: 0.7)

### 6. Breaking Changes
- The `completions()` method now returns an array instead of a string
- All code consuming the API responses needed to be updated to handle the new structure
- Model names and capabilities changed with the move to Chat Completions API
- OpenAIClient constructor now requires SettingsRepositoryInterface

## What You Need to Do

1. **Update Dependencies**: Run `composer update` to install the new OpenAI client version
2. **Configure Models**: You can now configure which OpenAI model to use in the admin panel
3. **Adjust Settings**: Configure `max_tokens` and `temperature` according to your needs
4. **Clear Cache**: Clear any cached model data as the API structure has changed
5. **Test Functionality**: Verify that ChatGPT responses are working correctly with the new API

## Supported Models

With the new Chat Completions API, you can use modern models such as:
- `gpt-3.5-turbo` (default, fast and cost-effective)
- `gpt-3.5-turbo-16k` (larger context window)
- `gpt-4` (more capable, requires API access)
- `gpt-4-turbo-preview` (latest GPT-4 model)

## Benefits of the Migration

- **Future-Proof**: Uses the current OpenAI API that's actively maintained
- **Configurable**: Model selection and parameters can be changed via admin settings
- **Better Models**: Access to newer, more capable models like GPT-3.5 Turbo and GPT-4
- **Improved Performance**: Chat Completions API is optimized for conversational use cases
- **Enhanced Features**: Better support for context and multi-turn conversations
- **Flexible Configuration**: Easily adjust response parameters without code changes

## Troubleshooting

If you encounter issues:

1. Ensure your OpenAI API key is still valid
2. Check that the selected model is available in your OpenAI account
3. Verify that your API usage limits haven't been exceeded
4. Clear the extension cache if model listing fails
5. Check the admin panel for proper model configuration