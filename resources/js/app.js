import Vue from "vue";
import VueX from "vuex";
import store from "~/store";
import router from "~/router";
import i18n from "~/plugins/i18n";
import App from "~/components/App";

import "~/plugins";
import "~/components";
import BPanel from "~/components/panel";
Vue.use(BPanel);
Vue.config.productionTip = false;

window.axios = require("axios");
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.baseURL = app.url;

import VueBootstrap from "bootstrap-vue";
import VueNVD3 from "vue-nvd3";
import VueInsProgressBar from "vue-ins-progress-bar";
//import VueEventCalendar from "vue-event-calendar";
import VueSparkline from "vue-sparklines";
//import * as VueGoogleMaps from 'vue2-google-maps'
//import Vueditor from '@agametov/vueditor'
import VueHljs from "vue-hljs";
import VueSweetalert2 from "vue-sweetalert2";
//import VueNotification from "vue-notification";
import VueDateTimePicker from "vue-bootstrap-datetimepicker";
import VueSelect from "vue-select";
import VueDatepicker from "vuejs-datepicker/dist/vuejs-datepicker.esm.js";
import VueMaskedInput from "vue-maskedinput";
import VueInputTag from "vue-input-tag";
//import VueSlider from "vue-slider-component";
//import VueGoodTable from 'vue-good-table';
//import VueCountdown from '@xkeshi/vue-countdown';
//import VueColorpicker from 'vue-pop-colorpicker'
import VueNoty from "vuejs-noty";
Vue.use(VueX);
Vue.use(VueBootstrap);
Vue.use(VueNVD3);
//Vue.use(VueEventCalendar, { locale: "en" });
Vue.use(VueSparkline);
//Vue.use(Vueditor)
Vue.use(VueHljs);
Vue.use(VueSweetalert2);
//Vue.use(VueNotification);
Vue.use(VueDateTimePicker);
/*Vue.use(VueGoodTable);
Vue.use(VueColorpicker);
Vue.use(VueGoogleMaps, {
  load: {
    key: "",
    libraries: "places"
  }
});
*/
Vue.use(VueNoty, {
  timeout: 1700,
  progressBar: true,
  layout: "bottomRight"
});
Vue.use(VueInsProgressBar, {
  position: "fixed",
  show: true,
  height: "3px"
});
Vue.component("v-select", VueSelect);
Vue.component("datepicker", VueDatepicker);
Vue.component("masked-input", VueMaskedInput);
Vue.component("input-tag", VueInputTag);
//Vue.component("vue-slider", VueSlider);
//Vue.component(VueCountdown.name, VueCountdown);
/* eslint-disable no-new */
let appVue = new Vue({
  i18n,
  store,
  router,
  ...App
});

global.vm = appVue;
