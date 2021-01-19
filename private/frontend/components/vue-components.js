

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

Vue.component('recipes-listing-item', {
  props: {
    page: { type: Object, required: true },
    recipe: { type: Object, required: true },
    record: { type: Object, required: true }
  },
  delimiters: ['${', '}'],
  template: '#recipes-listing-item-template',
  computed: {
    pubdate: function() {
      console.log(this.recipe.published)
      return moment(this.recipe.published, moment.ISO_8601).format(app.user.customSettings.formats.date.short)
    }
  }
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
  template: '#home-template',
  computed: {
    title: function() {
      return i18n.$t('pages.home.title')
    }
  }
}

const Logout = {
  delimiters: ['${', '}'],
  props: ['page', 'user', 'config'],
  template: '#logout-template'
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
      return 'width: ' + (this.pbCookValue <2 ? 1 : this.pbCookValue - 1) + '%'
    },
    pbCookLabel: function() {
      if (this.pbCookValue > 40)
        return app.$t('pages.recipe.preparation.timeconsumption.cookingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.cooking)
      if (this.pbCookValue > 20)
        return this.duration(this.recipe.preparation.timeConsumed.cooking)
      return ''
    },
    pbCookLabelFull: function() {
      if (this.recipe.preparation.timeConsumed.cooking == 0)
        return app.$t('pages.recipe.preparation.timeconsumption.cookingShort') + ': ' + app.$t('pages.recipe.preparation.timeconsumption.notset')
      return app.$t('pages.recipe.preparation.timeconsumption.cookingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.cooking)
    },
    pbPrepValue: function() {
      return Math.floor(this.recipe.preparation.timeConsumed.preparing / this.recipe.preparation.timeConsumed.total * 100)
    },
    pbPrepWidth: function() {
      return 'width: ' + (this.pbPrepValue <2 ? 1 : this.pbPrepValue - 1) + '%'
    },
    pbPrepLabel: function() {
      if (this.pbPrepValue > 40)
        return app.$t('pages.recipe.preparation.timeconsumption.preparingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.preparing)
      if (this.pbPrepValue > 20)
        return this.duration(this.recipe.preparation.timeConsumed.preparing)
      return ''
    },
    pbPrepLabelFull: function() {
      if (this.recipe.preparation.timeConsumed.preparing == 0)
        return app.$t('pages.recipe.preparation.timeconsumption.preparingShort') + ': ' + app.$t('pages.recipe.preparation.timeconsumption.notset')
      return app.$t('pages.recipe.preparation.timeconsumption.preparingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.preparing)
    },
    pbRestValue: function() {
      return Math.floor(this.recipe.preparation.timeConsumed.rest / this.recipe.preparation.timeConsumed.total * 100)
    },
    pbRestWidth: function() {
      return 'width: ' + (this.pbRestValue <2 ? 1 : this.pbRestValue - 1) + '%'
    },
    pbRestLabel: function() {
      if (this.pbRestValue > 40)
        return app.$t('pages.recipe.preparation.timeconsumption.restingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.rest)
      if (this.pbRestValue > 20)
        return this.duration(this.recipe.preparation.timeConsumed.rest)
      return ''
    },
    pbRestLabelFull: function() {
      if (this.recipe.preparation.timeConsumed.rest == 0)
        return app.$t('pages.recipe.preparation.timeconsumption.restingShort') + ': ' + app.$t('pages.recipe.preparation.timeconsumption.notset')
      return app.$t('pages.recipe.preparation.timeconsumption.restingShort') + ': ' + this.duration(this.recipe.preparation.timeConsumed.rest)
    },
    published: function() {
      if (this.recipe.published !== false)
        return moment(this.recipe.published, moment.ISO_8601).format(this.user.customSettings.formats.date.long)
      return ''
    }
  },
  methods: {
    duration: function(value) {
      duration = moment.duration(value, 'minutes')
      return duration.humanize()
    },
    onActionClicked: function(e) {
      console.log('@onActionClicked', e)
    },
    onDeleteButtonClicked: function() {
      $('#recipe-delete-modal-fail').addClass('d-none')
      $('#recipe-delete-modal').modal()
    },
    onModalDeleteButtonClicked: function() {
      // console.log('Recipe.onModalDeleteButtonClicked')
      $('#recipe-delete-modal-submit').prop('disabled', true)
      $('#recipe-delete-modal-close').prop('disabled', true)
      $('#recipe-delete-modal-spinner').removeClass('d-none')
      postPageData(app.$route.path, {
        delete: true
      }, function(data) {
        $('#recipe-delete-modal-spinner').addClass('d-none')
        if (data.success) {
          $('#recipe-delete-modal').modal('hide')
          app.$router.push({name: 'myRecipes'})
        } else {
          $('#recipe-delete-modal-fail-code').text(data.code)
          $('#recipe-delete-modal-fail-msg').text(app.$t(data.i18nmessage))
          $('#recipe-delete-modal-fail').removeClass('d-none')
          $('#recipe-delete-modal-submit').prop('disabled', false)
          $('#recipe-delete-modal-close').prop('disabled', false)
        }
      }, true)
    },
    onEaterCountChanged: function() {
      for (key in this.recipe.preparation.ingredients) {
        this.recipe.preparation.ingredients[key].quantityCalc =
          eatercalc(this.recipe.preparation.ingredients[key].quantity,
          this.recipe.eaterCount,
          this.recipe.eaterCountCalc)
      }
    },
    onMinusClick: function() {
      if (this.recipe.eaterCountCalc > 1) {
        this.recipe.eaterCountCalc -= 1
        this.onEaterCountChanged()
      }
    },
    onPlusClick: function() {
      if (this.recipe.eaterCountCalc <99) {
        this.recipe.eaterCountCalc += 1
        this.onEaterCountChanged()
      }
    },
    onPublishButtonClicked: function() {
      $('#recipe-publishbtn').prop('disabled', true)
      $('#recipe-publishbtn-icon').addClass('d-none')
      $('#recipe-publishbtn-spinner').removeClass('d-none')
      postPageData(app.$route.path, { publish: true },
        function(data) {
          $('#recipe-publishbtn-spinner').addClass('d-none')
          $('#recipe-publishbtn-icon').removeClass('d-none')
          $('#recipe-publishbtn').prop('disabled', false)
        }, true)
    },
    onRejectButtonClicked: function() {
      $('#recipe-rejectbtn').prop('disabled', true)
      $('#recipe-rejectbtn-icon').addClass('d-none')
      $('#recipe-rejectbtn-spinner').removeClass('d-none')
      postPageData(app.$route.path, { unpublish: true },
        function(data) {
          $('#recipe-rejectbtn-spinner').addClass('d-none')
          $('#recipe-rejectbtn-icon').removeClass('d-none')
          $('#recipe-rejectbtn').prop('disabled', false)
        }, true)
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
    },
    toggleGallery: function(i) {
      var picItems = []
      for (e in this.recipe.pictures) {
        picItems.push({
          src: this.recipe.pictures[e].link,
          h: this.recipe.pictures[e].h,
          w: this.recipe.pictures[e].w
        })
      }
      let pswp = document.querySelectorAll('.pswp')[0]
      let gal = new PhotoSwipe(pswp, PhotoSwipeUI_Default, picItems, { index: i })
      gal.init()
    }
  }
}

const RecipesList = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#recipes-listing-template'
}
