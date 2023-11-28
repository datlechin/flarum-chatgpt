/******/ (() => { // webpackBootstrap
/******/ 	// runtime can't be in strict mode because a global variable is assign and maybe created.
/******/ 	var __webpack_modules__ = ({

/***/ "./src/admin/components/ChatGPTSettings.tsx":
/*!**************************************************!*\
  !*** ./src/admin/components/ChatGPTSettings.tsx ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ChatGPTSettings)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/admin/components/ExtensionPage */ "flarum/admin/components/ExtensionPage");
/* harmony import */ var flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_2__);



var ChatGPTSettings = /*#__PURE__*/function (_ExtensionPage) {
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(ChatGPTSettings, _ExtensionPage);
  function ChatGPTSettings() {
    return _ExtensionPage.apply(this, arguments) || this;
  }
  var _proto = ChatGPTSettings.prototype;
  _proto.oninit = function oninit(vnode) {
    _ExtensionPage.prototype.oninit.call(this, vnode);
    this.loading = false;
  };
  _proto.content = function content() {
    return m("div", {
      className: "ExtensionPage-settings"
    }, m("div", {
      className: "container"
    }, m("div", {
      className: "Form"
    }, this.buildSettingComponent({
      setting: 'datlechin-chatgpt.api_key',
      type: 'text',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.api_key_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.api_key_help', {
        a: m("a", {
          href: "https://platform.openai.com/account/api-keys",
          target: "_blank",
          rel: "noopener"
        })
      }),
      placeholder: 'sk-...'
    }), this.buildSettingComponent({
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
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.model_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.model_help', {
        a: m("a", {
          href: "https://platform.openai.com/docs/models/overview",
          target: "_blank",
          rel: "noopener"
        })
      })
    }), this.buildSettingComponent({
      setting: 'datlechin-chatgpt.max_tokens',
      type: 'number',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help', {
        a: m("a", {
          href: "https://help.openai.com/en/articles/4936856",
          target: "_blank",
          rel: "noopener"
        })
      }),
      "default": 100
    }), this.buildSettingComponent({
      setting: 'datlechin-chatgpt.user_prompt',
      type: 'text',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_help')
    }), this.buildSettingComponent({
      setting: 'datlechin-chatgpt.user_prompt_badge_text',
      type: 'text',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_help')
    }), this.buildSettingComponent({
      setting: 'datlechin-chatgpt.enable_on_discussion_started',
      type: 'boolean',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_help')
    }), this.buildSettingComponent({
      type: 'flarum-tags.select-tags',
      setting: 'datlechin-chatgpt.enabled-tags',
      label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_label'),
      help: flarum_admin_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_help'),
      options: {
        requireParentTag: false,
        limits: {
          max: {
            secondary: 0
          }
        }
      }
    }), m("div", {
      className: "Form-group"
    }, this.submitButton()))));
  };
  return ChatGPTSettings;
}((flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_2___default()));


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
/* harmony import */ var _components_ChatGPTSettings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/ChatGPTSettings */ "./src/admin/components/ChatGPTSettings.tsx");


flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.add('datlechin/flarum-chatgpt', function () {
  flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().extensionData["for"]('datlechin-chatgpt').registerPermission({
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('datlechin-chatgpt.admin.permissions.use_chatgpt_assistant_label'),
    icon: 'fas fa-comment',
    permission: 'discussion.useChatGPTAssistant',
    allowGuest: false
  }, 'start').registerPage(_components_ChatGPTSettings__WEBPACK_IMPORTED_MODULE_1__["default"]);
});

/***/ }),

/***/ "flarum/admin/app":
/*!**************************************************!*\
  !*** external "flarum.core.compat['admin/app']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['admin/app'];

/***/ }),

/***/ "flarum/admin/components/ExtensionPage":
/*!***********************************************************************!*\
  !*** external "flarum.core.compat['admin/components/ExtensionPage']" ***!
  \***********************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['admin/components/ExtensionPage'];

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js ***!
  \******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _inheritsLoose)
/* harmony export */ });
/* harmony import */ var _setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js");

function _inheritsLoose(subClass, superClass) {
  subClass.prototype = Object.create(superClass.prototype);
  subClass.prototype.constructor = subClass;
  (0,_setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__["default"])(subClass, superClass);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _setPrototypeOf)
/* harmony export */ });
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };
  return _setPrototypeOf(o, p);
}

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