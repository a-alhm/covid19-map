require('./bootstrap');

import Vue from 'vue'
import App from './vue/App.vue'
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify)

const vuetify = new Vuetify({
    icons: {
      iconfont: 'mdiSvg',
    },
  })

Vue.config.productionTip = false

new Vue({
  vuetify,
  el: "#app",
  components: { App }
})
