Vue.component('breadcrumb', {
  props: ['target', 'title', 'params'],
  template:
    '<li class="breadcrumbs-item">' +
    '<router-link :to="{ name: target, params: params }">{{title}}' +
    '</router-link></li>'
})

Vue.component('btn-sm-blue', {
  props: {
    badge: { type: String, required: false },
    badgeicon: { type: Object, required: false },
    outline: { type: Boolean, default: false, required: false },
    title: { type: String, required: true },
    subject: { type: String, required: false },
    icon: { type: String, required: false },
    space: { type: String, required: false }
  },
  template:
    `<b-button size="sm"
      v-bind:class="[{ 'btn-blue': !outline }, { 'btn-outline-blue': outline } ]"
      @click="onClick">
      <fa-icon v-if="icon" :icon="icon" :space="space" :class="'fs-80 ' + title == '' ? 'mx-1' : 'mr-1'"></fa-icon>
      {{ title }}
      <b-badge class="ml-1 text-blue" v-if="badge" variant="light">
        {{ badge }} <b-icon class="text-blue"
          :icon="badgeicon.icon" v-if="badgeicon"></b-icon>
      </b-badge>
    </b-button>`,
    methods: {
      onClick: function() {
        console.log('@onClick')
        this.$emit('click', this.subject ? this.subject : this.title)
      }
    }
})

Vue.component('btn-scrollto', {
  props: {
    badge: { type: String, required: false },
    badgeicon: { type: Object, required: false },
    outline: { type: Boolean, default: false, required: false },
    target: { type: String, required: true },
    title: { type: String, required: true },
    subject: { type: String, required: false },
    icon: { type: String, required: false },
    space: { type: String, required: false }
  },
  template: '#btn-scrollto-template',
  methods: {
    onClick: function(e) {
      var tar = $('#' + this.target)
      if (tar) {
        var pos = tar.offset().top - 60;
        $(window).scrollTop(pos);
      }
      this.$emit('click', this.subject ? this.subject : this.title)
    }
  }
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

Vue.component('page-actions-container', {
  props: {
    page: { type: Object, required: true },
    user: { type: Object, required: true }
  },
  delimiters: ['${', '}'],
  template: '#page-actions-container-template',
  methods: {
    onClick: function(e) {
      console.log('@onClick', e)
      this.$emit('click', e)
    }
  }
})

Vue.component('recipe-actions-container', {
  props: {
    page: { type: Object, required: true },
    user: { type: Object, required: true }
  },
  delimiters: ['${', '}'],
  template: '#recipe-actions-container-template',
  methods: {
    onClick: function(e) {
      console.log('@onClick', e)
      switch(e) {
        case 'unpublish':
          var recipe = this.page.currentRecipe
          var user = this.user
          if (recipe == null || recipe.id == null || recipe.isPublished == false
              || user == null || recipe.ownerId == null || user.id == null
              || user.id != recipe.ownerId)
            return
          postPageData(app.$route.path, {
            unpublish: true
          }, function(data) {
            console.log('unpublish: ', data)
            if (data.success == false) {
              app.$set(app.page.modals.failedModal, 'message', data.message)
              app.$set(app.page.modals.failedModal, 'code', data.code)
              $('#action-failed-modal').modal()
            }
          }, true)
          break
        case 'publish':
          console.log(e)
          var recipe = this.page.currentRecipe
          var user = this.user
          if (recipe == null || recipe.id == null || recipe.isPublished == false
              || user == null || recipe.ownerId == null || user.id == null
              || user.id != recipe.ownerId)
            return
          postPageData(app.$route.path, {
            publish: true
          }, function(data) {
            console.log('publish: ', data)
            if (data.success == false) {
              app.$set(app.page.modals.failedModal, 'message', data.message)
              app.$set(app.page.modals.failedModal, 'code', data.code)
              $('#action-failed-modal').modal()
            }
          }, true)
          break
        case 'edit':
          var recipe = this.page.currentRecipe
          router.push({ name: 'editRecipe', params: { id: recipe.id, name: recipe.name } })
          break
        case 'toGallery':
          var recipe = this.page.currentRecipe
          router.push({ name: 'gallery', params: { id: recipe.id, name: recipe.name } })
          break
        case 'toRecipe':
          var recipe = this.page.currentRecipe
          router.push({ name: 'recipe', params: { id: recipe.id, name: recipe.name } })
          break
      }
    }
  }
})

Vue.component('recipes-listing-item', {
  props: {
    page: { type: Object, required: true },
    recipe: { type: Object, required: true },
    record: { type: Object, required: true }
  },
  delimiters: ['${', '}'],
  template: '#recipes-listing-item-template'
})

Vue.component('sidebar', {
  props: ['page'],
  delimiters: ['${', '}'],
  template: '#sidebar-template',
  methods: {
    toggleSidebar: function(event) {
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
  delimiters: ['${', '}'],
  props: ['page', 'user'],
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
    onActionClicked: function(e) {
      console.log('@onActionClicked', e)
    },
    onEaterCountChanged: function() {
      for (key in this.recipe.preparation.ingredients) {
        this.recipe.preparation.ingredients[key].quantityCalc =
          this.recipe.preparation.ingredients[key].quantity /
          this.recipe.eaterCount *
          this.recipe.eaterCountCalc
      }
    },
    onRatingStartButtonPress: function() {
      $('#recipe-rating-modal').modal()
    },
    onRatingSubmitPress: function() {
      $('#recipe-rating-modal-save').addClass('d-none')
      $('#recipe-rating-modal-spinner').removeClass('d-none')
      $('#recipe-rating-modal-mainbody').addClass('d-none')
      $('#recipe-rating-modal-submitting').removeClass('d-none')
      postPageData(app.$route.path, {
        vote: {
          cooked: this.page.self.currentVote.cooked,
          rating: this.page.self.currentVote.rating,
          voting: this.page.self.currentVote.voting
        }
      }, function(data) {
        if (data.success) {
          app.$set(app.page.self.lastVote, 'cooked', app.page.self.currentVote.cooked)
          app.$set(app.page.self.lastVote, 'rating', app.page.self.currentVote.rating)
          app.$set(app.page.self.lastVote, 'voting', app.page.self.currentVote.voting)
          app.$set(app.page.self, 'hasVoted', true)
          $('#recipe-rating-modal-spinner').addClass('d-none')
          $('#recipe-rating-modal-submitting').addClass('d-none')
          $('#recipe-rating-modal-submitted').removeClass('d-none')
        } else {
          $('#recipe-rating-modal-spinner').addClass('d-none')
          $('#recipe-rating-modal-save').addClass('d-none')
          $('#recipe-rating-modal-submitting').addClass('d-none')
          $('#recipe-rating-modal-error').removeClass('d-none')
          $('#recipe-rating-modal-mainbody').removeClass('d-none')
        }
      })
    }
  }
}

const RecipesList = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#recipes-listing-template'
}

const SearchRecipe = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#search-template',
  methods: {
    onClick: function() {
      console.log('SearchRecipe @click')
      postPageData(app.$route.path, {
        search: {
          phrase: this.page.search.filter.title
        }
      }, function(data) {
        console.log('onRatingSubmitPress: ', data)
        if (data.success) {

        } else {

        }
      })
    }
  }
}
