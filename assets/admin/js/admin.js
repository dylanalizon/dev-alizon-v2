import './plugin';
import '../css/style.scss';
import Vue from 'vue';

// Vue components
import ImageManager from './components/ImageManager';
import InputImageManager from './components/InputImageManager';

window.KTUtil = require("./global/components/base/util");
window.KTApp = require("./global/components/base/app");
window.KTAvatar = require("./global/components/base/avatar");
window.KTDialog = require("./global/components/base/dialog");
window.KTHeader = require("./global/components/base/header");
window.KTMenu = require("./global/components/base/menu");
window.KTOffcanvas = require("./global/components/base/offcanvas");
window.KTPortlet = require("./global/components/base/portlet");
window.KTScrolltop = require("./global/components/base/scrolltop");
window.KTToggle = require("./global/components/base/toggle");
window.KTWizard = require("./global/components/base/wizard");
require("./global/components/base/datatable/core.datatable");
require("./global/components/base/datatable/datatable.checkbox");
require("./global/components/base/datatable/datatable.rtl");

// Layout Scripts
window.KTLayout = require("./global/layout/layout");
window.KTChat = require("./global/layout/chat");
require("./global/layout/demo-panel");
require("./global/layout/offcanvas-panel");
require("./global/layout/quick-panel");
require("./global/layout/quick-search");

if (document.querySelector('#image-manager')) {
  new Vue({
    el: '#image-manager',
    components: {
      ImageManager
    }
  });
}

if (document.querySelector('#input-image-manager')) {
  new Vue({
    el: '#input-image-manager',
    components: {
      InputImageManager
    }
  });
}
