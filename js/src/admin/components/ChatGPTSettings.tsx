import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';

export default class ChatGPTSettings extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = false;
  }

  content() {
    return (
      <div className="ExtensionPage-settings">
        <div className="container">
          <div className="Form">
            {this.buildSettingComponent({
              setting: 'datlechin-chatgpt.api_key',
              type: 'text',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_help', {
                a: <a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noopener" />,
              }),
              placeholder: 'sk-...',
            })}
            {this.buildSettingComponent({
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
            })}
            {this.buildSettingComponent({
              setting: 'datlechin-chatgpt.max_tokens',
              type: 'number',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help', {
                a: <a href="https://help.openai.com/en/articles/4936856" target="_blank" rel="noopener" />,
              }),
              default: 100,
            })}
            {this.buildSettingComponent({
              setting: 'datlechin-chatgpt.user_prompt',
              type: 'text',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_help'),
            })}
            {this.buildSettingComponent({
              setting: 'datlechin-chatgpt.user_prompt_badge_text',
              type: 'text',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_help'),
            })}
            {this.buildSettingComponent({
              setting: 'datlechin-chatgpt.enable_on_discussion_started',
              type: 'boolean',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_help'),
            })}
            {this.buildSettingComponent({
              type: 'flarum-tags.select-tags',
              setting: 'datlechin-chatgpt.enabled-tags',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_label'),
              help: app.translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_help'),
              options: {
                requireParentTag: false,
                limits: {
                  max: {
                    secondary: 0,
                  },
                },
              },
            })}
            <div className="Form-group">{this.submitButton()}</div>
          </div>
        </div>
      </div>
    );
  }
}
