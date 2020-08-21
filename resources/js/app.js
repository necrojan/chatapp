/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
let VueCookie = require('vue-cookie');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('message', require('./components/Message.vue').default);
Vue.component('chat', require('./components/Chat.vue').default);
Vue.component('responses', require('./components/Responses.vue').default);
Vue.component('personal-responses', require('./components/PersonalResponses.vue').default);
Vue.component('pins', require('./components/Pins.vue').default);
Vue.component('auto-suggest', require('./components/AutoSuggest.vue').default);
Vue.component('active-user-list', require('./components/ActiveUserList.vue').default);
Vue.component('join', require('./components/Join.vue').default);
Vue.component('file-upload', require('./components/FileUpload.vue').default);
Vue.component('search-text', require('./components/SearchText.vue').default);


import formatter from "./helper/formatter";
import sweet from './helper/toast';
import bus from './bus';
import VueSweetalert2 from "vue-sweetalert2";
import { BootstrapVue, TablePlugin } from 'bootstrap-vue'
import linkify from 'vue-linkify';

Vue.use(formatter);
Vue.use(sweet);
Vue.use(bus);
Vue.use(VueCookie);
Vue.use(VueSweetalert2);
Vue.use(BootstrapVue);
Vue.use(TablePlugin);
Vue.directive('linkified', linkify);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    bus
});
