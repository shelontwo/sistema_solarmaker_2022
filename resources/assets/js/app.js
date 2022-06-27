/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Popper = require("popper.js");

window.$ = window.jQuery = require("jquery");

require("admin-lte");
require("bootstrap");

window.Vue = require("vue");

import Vue from "vue";

import VueSweetalert2 from "vue-sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
Vue.use(VueSweetalert2);

import Swal from "sweetalert2/dist/sweetalert2.js";

import money from "v-money";
Vue.use(money, { precision: 4 });

import VueTheMask from "vue-the-mask";
Vue.use(VueTheMask);

import vClickOutside from "v-click-outside";
import Axios from "axios";
import { type } from "jquery";
Vue.use(vClickOutside);

import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
Vue.use(VueFlatPickr);

import Pagination from 'vue-pagination-2';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component("qrcode-component", require("./cms/QRCode.vue").default);
Vue.component("modal-premiacao", require("./cms/ModalPremiacao.vue").default);
Vue.component("modal-saldo", require("./cms/ModalSaldo.vue").default);
Vue.component('pagination', Pagination);
Vue.component("content-header", require("./cms/ContentHeader.vue").default);
Vue.component("data-table", require("./cms/DataTable.vue").default);
Vue.component("data-table-shop", require("./cms/DataTableShop.vue").default);
Vue.component(
  "data-table-publication",
  require("./cms/DataTablePublication.vue").default
);
Vue.component("tabs", require("./cms/Tabs.vue").default);
Vue.component("ui-form", require("./cms/UIForm.vue").default);
Vue.component("ui-form-cart", require("./cms/UIFormCart.vue").default);
Vue.component("ui-select", require("./cms/UISelect.vue").default);
Vue.component(
  "ui-select-parse",
  require("./cms/UISelectWithParse.vue").default
);
Vue.component("multi-select", require("./cms/UIMultiSelect.vue").default);
Vue.component("ui-textarea", require("./cms/UITextarea.vue").default);
Vue.component("ui-money", require("./cms/UIMoney.vue").default);
Vue.component("ui-direct-sell", require("./cms/UIDirectSell.vue").default);
Vue.component("ui-discount", require("./cms/UIDiscount.vue").default);
Vue.component("ui-phone", require("./cms/UIPhone.vue").default);
Vue.component(
  "ui-direct-sell-list",
  require("./cms/UIDirectSellList.vue").default
);
Vue.component("ui-percent", require("./cms/UIPercent.vue").default);
Vue.component("ui-mask-input", require("./cms/UIMaskInput.vue").default);
Vue.component("ui-coupons-uses", require("./cms/UICouponUses.vue").default);
Vue.component("alert", require("./cms/Alert.vue").default);
Vue.component("checkboxes", require("./cms/Checkboxes.vue").default);
Vue.component("radios", require("./cms/Radios.vue").default);
Vue.component("dropdown-list", require("./cms/DropdownList.vue").default);
Vue.component("dropdown-events", require("./cms/DropdownEvents.vue").default);
Vue.component("cidade-bairro", require("./cms/Cidade-bairro.vue").default);
Vue.component(
  "the-testimonies-carousel",
  require("./front/TheTestimoniesCarousel.vue").default
);
Vue.component(
  "the-speakers-carousel",
  require("./front/TheSpeakersCarousel.vue").default
);
Vue.component(
  "the-previous-editions-carousel",
  require("./front/ThePreviousEditionsCarousel.vue").default
);
Vue.component(
  "the-brands-carousel",
  require("./front/TheBrandsCarousel.vue").default
);
Vue.component("the-gallery", require("./front/TheGallery.vue").default);
Vue.component("the-newsletter", require("./front/TheNewsletter.vue").default);
Vue.component(
  "the-insterested-sponsor",
  require("./front/TheSponsorInterest.vue").default
);
Vue.component("the-lp-form", require("./front/TheLPForm.vue").default);
Vue.component("the-banner", require("./front/TheBanner.vue").default);
Vue.component("the-signup", require("./front/TheSignUpForm.vue").default);
Vue.component("the-login", require("./front/TheLoginForm.vue").default);
Vue.component("the-log-out", require("./front/TheLogOut.vue").default);
Vue.component("the-buy", require("./front/TheBuyPassport.vue").default);
Vue.component("the-cart", require("./front/TheCart.vue").default);
Vue.component("the-show", require("./front/TheShowPassport.vue").default);
Vue.component("the-profile", require("./front/TheProfileForm.vue").default);
Vue.component("the-payment", require("./front/ThePayment.vue").default);
Vue.component("the-checkin", require("./front/TheCheckinForm.vue").default);
Vue.component("the-forgot-password", require("./front/TheForgotPassword.vue").default);
Vue.component("the-password", require("./front/TheRemakePassword.vue").default);

Vue.component(
  "the-city-selector",
  require("./cms/UISelectCityAndState.vue").default
);

const app = new Vue({
  el: "#app",
});
