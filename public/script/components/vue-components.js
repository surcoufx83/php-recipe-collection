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
    fw: { type: Boolean, default: false, required: false },
    icon: { type: String, required: false },
    space: { type: String, required: false },
    spin: { type: Boolean, default: false, required: false },
    pulse: { type: Boolean, default: false, required: false }
  },
  delimiters: ['${', '}'],
  template: '#fa-icon-template'
})

Vue.component('recipe-ingredient', {
  props: {
    description: { type: String, required: true },
    quantity: { type: Number, required: false },
    unit: { type: Object, required: false }
  },
  delimiters: ['${', '}'],
  template: '#recipe-ingredient-template'
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
  props: ['recipe', 'page', 'user'],
  template: '#recipe-template',
  computed: {
    pbCookValue: function() {
      return Math.floor(this.recipe.preparation.timeConsumed.cooking / this.recipe.preparation.timeConsumed.total * 100)
    },
    pbCookWidth: function() {
      return 'width: ' + (this.pbCookValue == 0 ? 1 : this.pbCookValue - 1) + '%'
    },
    pbCookLabel: function() {
      if (this.pbCookValue > 20)
        return this.recipe.preparation.timeConsumed.formatted.cooking.timeStr
      return ''
    },
    pbPrepValue: function() {
      return Math.floor(this.recipe.preparation.timeConsumed.preparing / this.recipe.preparation.timeConsumed.total * 100)
    },
    pbPrepWidth: function() {
      return 'width: ' + (this.pbPrepValue == 0 ? 1 : this.pbPrepValue - 1) + '%'
    },
    pbPrepLabel: function() {
      if (this.pbPrepValue > 20)
        return this.recipe.preparation.timeConsumed.formatted.preparing.timeStr
      return ''
    },
    pbRestValue: function() {
      return Math.floor(this.recipe.preparation.timeConsumed.rest / this.recipe.preparation.timeConsumed.total * 100)
    },
    pbRestWidth: function() {
      return 'width: ' + (this.pbRestValue == 0 ? 1 : this.pbRestValue - 1) + '%'
    },
    pbRestLabel: function() {
      if (this.pbRestValue > 20)
        return this.recipe.preparation.timeConsumed.formatted.rest.timeStr
      return ''
    }
  },
  methods: {
    onEaterCountChanged: function(event) {
      for (key in this.recipe.preparation.ingredients) {
          this.recipe.preparation.ingredients[key].quantityCalc =
            this.recipe.preparation.ingredients[key].quantity /
            this.recipe.eaterCount *
            event
      }
    },
    onRatingStartButtonPress: function(event) {
      console.log('onRatingStartButtonPress')
      $('#recipe-rating-modal').modal()
    }
  }
}
