import app from 'flarum/admin/app';
import ChatGPTSettings from './components/ChatGPTSettings';

app.initializers.add('datlechin/flarum-chatgpt', () => {
  app.extensionData
    .for('datlechin-chatgpt')
    .registerPermission(
      {
        label: app.translator.trans('datlechin-chatgpt.admin.permissions.use_chatgpt_assistant_label'),
        icon: 'fas fa-comment',
        permission: 'discussion.useChatGPTAssistant',
        allowGuest: false,
      },
      'start'
    )
    .registerPage(ChatGPTSettings);
});
