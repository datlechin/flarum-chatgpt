import app from 'flarum/admin/app';
import addChatgptToTagsModal from './addChatgptToTagsModal';

app.initializers.add('datlechin/flarum-chatgpt', () => {
  app.extensionData
    .for('datlechin-chatgpt')
    .registerSetting({
      setting: 'datlechin-chatgpt.api_key',
      type: 'text',
      label: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_help', {
        a: <a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noopener" />,
      }),
      placeholder: 'sk-...',
    })
    .registerSetting({
      setting: 'datlechin-chatgpt.model',
      type: 'dropdown',
      options: {
        'text-davinci-003': 'text-davinci-003',
        'gpt-3.5-turbo': 'gpt-3.5-turbo',
        'gpt-3.5-turbo-16k': 'gpt-3.5-turbo-16k',
        'text-davinci-002': 'text-davinci-002',
        'code-davinci-002': 'code-davinci-002',
        'gpt-4': 'gpt-4',
        'gpt-4-0613': 'gpt-4-0613',
        'gpt-4-32k': 'gpt-4-32k',
        'gpt-4-32k-0613': 'gpt-4-32k-0613',
        'gpt-4-0314': 'gpt-4-0314',
        'gpt-4-32k-0314': 'gpt-4-32k-0314',
      },
      label: app.translator.trans('datlechin-chatgpt.admin.settings.model_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.model_help', {
        a: <a href="https://platform.openai.com/docs/models/overview" target="_blank" rel="noopener" />,
      }),
    })
    .registerSetting({
      setting: 'datlechin-chatgpt.max_tokens',
      type: 'number',
      label: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help', {
        a: <a href="https://help.openai.com/en/articles/4936856" target="_blank" rel="noopener" />,
      }),
      default: 100,
    })
    .registerSetting({
      setting: 'datlechin-chatgpt.user_prompt',
      type: 'text',
      label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_help'),
    })
    .registerSetting({
      setting: 'datlechin-chatgpt.user_prompt_badge_text',
      type: 'text',
      label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_help'),
    })
    .registerSetting({
      setting: 'datlechin-chatgpt.enable_on_discussion_started',
      type: 'boolean',
      label: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_label'),
      help: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_help'),
    })
    .registerPermission(
      {
        label: app.translator.trans('datlechin-chatgpt.admin.permissions.use_chatgpt_assistant_label'),
        icon: 'fas fa-comment',
        permission: 'discussion.useChatGPTAssistant',
        allowGuest: false,
      },
      'start'
    );
    addChatgptToTagsModal();
});
