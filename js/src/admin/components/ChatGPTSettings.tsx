import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Stream from 'flarum/common/utils/Stream';

export default class ChatGPTSettings extends ExtensionPage {
  


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
              type: 'text',
              setting: 'datlechin-chatgpt.api_base',
              label: app.translator.trans('datlechin-chatgpt.admin.settings.api_base_label'),
              placeholder: 'https://api.openai.com/v1',
              help: app.translator.trans('datlechin-chatgpt.admin.settings.api_base_help')
            })}

            
            {this.buildSettingComponent({
                setting: 'datlechin-chatgpt.model',
                type: 'text',
                label: app.translator.trans('datlechin-chatgpt.admin.settings.model_label'),
                help: app.translator.trans('datlechin-chatgpt.admin.settings.model_help', {
                    a: <a href="YOUR_API_DOCS_URL" target="_blank" rel="noopener" />,
                }),
                placeholder: 'gpt-3.5-turbo',
            })}
            
            {this.buildSettingComponent({
                type: 'number',
                setting: 'datlechin-chatgpt.max_tokens',
                label: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
                help: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help'),
                min: 1,
                max: 4096
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
