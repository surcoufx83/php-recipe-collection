Vue.component('breadcrumb', {
  props: ['target', 'title', 'params'],
  template:
    '<li class="breadcrumbs-item">' +
    '<router-link :to="{ name: target, params: params }">{{title}}' +
    '</router-link></li>'
})

Vue.component('btn-sm-blue', {
  props: {
    badge: {
      type: String,
      required: false
    },
    badgeicon: {
      type: Object,
      required: false
    },
    outline: {
      type: Boolean,
      default: false
    },
    title: String
  },
  template:
    `<b-button size="sm"
      v-bind:class="[{ 'btn-blue': !outline }, { 'btn-outline-blue': outline } ]">
      {{ title }}
      <b-badge class="ml-1 text-blue" v-if="badge" variant="light">
        {{ badge }} <b-icon class="text-blue"
          :icon="badgeicon.icon" v-if="badgeicon"></b-icon>
      </b-badge>
    </b-button>`
})

Vue.component('fa-icon', {
  props: {
    far: { type: Boolean, default: false, required: false },
    fas: { type: Boolean, default: true, required: false },
    icon: { type: String, required: false }
  },
  delimiters: ['${', '}'],
  template: '#fa-icon-template'
})

Vue.component('sidebar', {
  props: ['page'],
  delimiters: ['${', '}'],
  template: '#sidebar-template',
  methods: {
    toggleSidebar: function(event) {
      console.log('sidebar: toggleSidebar')
      app.$set(app.page.sidebar, 'visible', event)
    }
  }
})

Vue.component('sidebar-ul1-li', {
  delimiters: ['${', '}'],
  props: {
    to: { type: String, required: true },
    params: { type: Object, required: false },
    text: {type: String, required: true },
    exact: { type: Boolean, default: false, required: false },
    far: { type: Boolean, default: false, required: false },
    fas: { type: Boolean, default: true, required: false },
    icon: { type: String, required: false },
    children: { type: Array, required: false }
  },
  template: '#sidebar-ul1-li-template'
})

Vue.component('sidebar-ul2-li', {
  delimiters: ['${', '}'],
  props: {
    to: { type: String, required: true },
    params: { type: Object, required: false },
    text: { type: String, required: true },
    exact: { type: Boolean, default: false, required: false },
    far: { type: Boolean, default: false, required: false },
    fas: { type: Boolean, default: true, required: false },
    icon: { type: String, required: false }
  },
  template: '#sidebar-ul2-li-template'
})

const Home = {
  template: '#home-template'
}

const Recipe = {
  delimiters: ['${', '}'],
  props: ['recipe', 'page'],
  template: '#recipe-template'
}
