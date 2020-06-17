require('./bootstrap');

import Vue from 'vue'
import router from './router.js'
import App from './App.vue'

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App />'
})