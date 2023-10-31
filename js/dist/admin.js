/******/ (() => { // webpackBootstrap
/******/ 	// runtime can't be in strict mode because a global variable is assign and maybe created.
/******/ 	var __webpack_modules__ = ({

/***/ "./src/admin/addChatgptToTagsModal.js":
/*!********************************************!*\
  !*** ./src/admin/addChatgptToTagsModal.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* export default binding */ __WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/tags/components/EditTagModal */ "flarum/tags/components/EditTagModal");
/* harmony import */ var flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/extend */ "flarum/common/extend");
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_extend__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_utils_Stream__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/utils/Stream */ "flarum/utils/Stream");
/* harmony import */ var flarum_utils_Stream__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_utils_Stream__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_Model__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/Model */ "flarum/Model");
/* harmony import */ var flarum_Model__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_Model__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_tags_models_Tag__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/tags/models/Tag */ "flarum/tags/models/Tag");
/* harmony import */ var flarum_tags_models_Tag__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_tags_models_Tag__WEBPACK_IMPORTED_MODULE_5__);






/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
  if (flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.has('flarum-tags')) {
    (flarum_tags_models_Tag__WEBPACK_IMPORTED_MODULE_5___default().prototype.isChatgpt) = flarum_Model__WEBPACK_IMPORTED_MODULE_4___default().attribute('isChatgpt');
    (0,flarum_common_extend__WEBPACK_IMPORTED_MODULE_2__.extend)((flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1___default().prototype), 'oninit', function () {
      this.isChatgpt = flarum_utils_Stream__WEBPACK_IMPORTED_MODULE_3___default()(this.tag.isChatgpt() || false);
    });
    (0,flarum_common_extend__WEBPACK_IMPORTED_MODULE_2__.extend)((flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1___default().prototype), 'fields', function (items) {
      items.add('chatgpt', m("div", {
        className: "Form-group"
      }, m("div", null, m("label", {
        className: "checkbox"
      }, m("input", {
        type: "checkbox",
        bidi: this.isChatgpt
      }), flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.edit_tag.chatgpt_label')))), 10);
    });
    (0,flarum_common_extend__WEBPACK_IMPORTED_MODULE_2__.extend)((flarum_tags_components_EditTagModal__WEBPACK_IMPORTED_MODULE_1___default().prototype), 'submitData', function (data) {
      data.isChatgpt = this.isChatgpt();
    });
  }
}

/***/ }),

/***/ "./src/admin/index.tsx":
/*!*****************************!*\
  !*** ./src/admin/index.tsx ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _addChatgptToTagsModal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./addChatgptToTagsModal */ "./src/admin/addChatgptToTagsModal.js");


flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.add('datlechin/flarum-chatgpt', function () {
  flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().extensionData["for"]('datlechin-chatgpt').registerSetting({
    setting: 'datlechin-chatgpt.api_key',
    type: 'text',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.api_key_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.api_key_help', {
      a: m("a", {
        href: "https://platform.openai.com/account/api-keys",
        target: "_blank",
        rel: "noopener"
      })
    }),
    placeholder: 'sk-...'
  }).registerSetting({
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
      'gpt-4-32k-0314': 'gpt-4-32k-0314'
    },
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.model_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.model_help', {
      a: m("a", {
        href: "https://platform.openai.com/docs/models/overview",
        target: "_blank",
        rel: "noopener"
      })
    })
  }).registerSetting({
    setting: 'datlechin-chatgpt.max_tokens',
    type: 'number',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help', {
      a: m("a", {
        href: "https://help.openai.com/en/articles/4936856",
        target: "_blank",
        rel: "noopener"
      })
    }),
    "default": 100
  }).registerSetting({
    setting: 'datlechin-chatgpt.user_prompt',
    type: 'text',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_help')
  }).registerSetting({
    setting: 'datlechin-chatgpt.user_prompt_badge_text',
    type: 'text',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_help')
  }).registerSetting({
    setting: 'datlechin-chatgpt.enable_on_discussion_started',
    type: 'boolean',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_label'),
    help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_help')
  }).registerPermission({
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.permissions.use_chatgpt_assistant_label'),
    icon: 'fas fa-comment',
    permission: 'discussion.useChatGPTAssistant',
    allowGuest: false
  }, 'start');
  (0,_addChatgptToTagsModal__WEBPACK_IMPORTED_MODULE_1__["default"])();
});

/***/ }),

/***/ "flarum/Model":
/*!**********************************************!*\
  !*** external "flarum.core.compat['Model']" ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['Model'];

/***/ }),

/***/ "flarum/admin/app":
/*!**************************************************!*\
  !*** external "flarum.core.compat['admin/app']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['admin/app'];

/***/ }),

/***/ "flarum/common/extend":
/*!******************************************************!*\
  !*** external "flarum.core.compat['common/extend']" ***!
  \******************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/extend'];

/***/ }),

/***/ "flarum/tags/components/EditTagModal":
/*!*********************************************************************!*\
  !*** external "flarum.core.compat['tags/components/EditTagModal']" ***!
  \*********************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['tags/components/EditTagModal'];

/***/ }),

/***/ "flarum/tags/models/Tag":
/*!********************************************************!*\
  !*** external "flarum.core.compat['tags/models/Tag']" ***!
  \********************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['tags/models/Tag'];

/***/ }),

/***/ "flarum/utils/Stream":
/*!*****************************************************!*\
  !*** external "flarum.core.compat['utils/Stream']" ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['utils/Stream'];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!******************!*\
  !*** ./admin.ts ***!
  \******************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _src_admin__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./src/admin */ "./src/admin/index.tsx");

})();

module.exports = __webpack_exports__;
/******/ })()
;
//# sourceMappingURL=admin.js.map