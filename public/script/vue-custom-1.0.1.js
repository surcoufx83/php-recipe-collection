function eatercalc(value, eater, neweater) {
  if (eater == neweater)
    return value
  if (neweater <= 0)
    return 0
  var calc = value * neweater / eater
  var factor = evaluateFactor(calc)
  console.log(value, eater, neweater)
  console.log(calc, factor)
  if (calc < 1)
    return calc
  calc = Math.round(calc / factor) * factor

  return calc
}

function evaluateFactor(value) {
  if (value < 1)
    return .1
  if (value < 2)
    return .5
  if (value < 10)
    return 1
  if (value < 25)
    return 2.5
  if (value < 50)
    return 5
  if (value < 100)
    return 10
  if (value < 1000)
    return 25
  return 50
}
Vue.component('btn-sm-blue', {
  props: {
    badge: { type: String, required: false },
    badgeicon: { type: Object, required: false },
    outline: { type: Boolean, default: false, required: false },
    title: { type: String, required: false, default: '' },
    subject: { type: String, required: false },
    icon: { type: String, required: false },
    space: { type: String, required: false },
    iconClass: { type: String, required: false, default: '' }
  },
  template:
    `<b-button size="sm"
      v-bind:class="[{ 'btn-blue': !outline }, { 'btn-outline-blue': outline } ]"
      @click="onClick">
      <fa-icon v-if="icon" :icon="icon" :space="space"
      :class="['fs-80 ', title === '' ? 'mx-1' : 'mr-1']"></fa-icon>
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
  template: '#home-template',
  computed: {
    title: function() {
      return i18n.$t('pages.home.title')
    }
  }
}

const Login = {
  delimiters: ['${', '}'],
  props: ['page', 'user', 'config'],
  template: '#login-template'
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
    onEaterCountChanged: function() {
      for (key in this.recipe.preparation.ingredients) {
        this.recipe.preparation.ingredients[key].quantityCalc =
          eatercalc(this.recipe.preparation.ingredients[key].quantity,
          this.recipe.eaterCount,
          this.recipe.eaterCountCalc)
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
Vue.component('rc-breadcrumbitem', {
  delimiters: ['${', '}'],
  props: {
    target: { type: String, required: true },
    title: { type: String, required: true },
    params: { type: Object, required: false },
    sm: { type: Boolean, required: false, default: false }
  },
  template: '#rc-breadcrumbitem-template'
})

const bciHome = { target: 'home', title: i18n.t("breadcrumb.home"), sm: true }
const bciRecipes = { target: 'recipes', title: i18n.t("breadcrumb.recipes.common") }
const bciRecipesMy = { target: 'myRecipes', title: i18n.t("breadcrumb.recipes.my") }
const bciSearch = { target: 'search', title: i18n.t("breadcrumb.search.common") }
const bciWriteRecipe = { target: 'writeRecipe', title: i18n.t("breadcrumb.write") }


Vue.component('rc-breadcrumbbar', {
  delimiters: ['${', '}'],
  props: ['page', 'recipe'],
  template: '#rc-breadcrumbbar-template',
  computed: {
    items: function() {

      items = [ bciHome ]

      switch (this.$route.name) {

        case 'home': return items

        case 'recipe':
        case 'gallery':
        case 'editRecipe':
          if (this.recipe && this.recipe.id) {
            if (this.recipe.ownerId > 0)
              items.push({ target: 'userRecipes', title: this.$t("breadcrumb.recipes.users", { user: this.recipe.ownerName }), params: { id: this.recipe.ownerId, name: this.recipe.ownerName } })
            items.push({ target: 'recipe', title: this.recipe.name, params: { id: this.recipe.id, name: this.recipe.name } })
            if (this.$route.name == 'gallery')
              items.push({ target: 'gallery', title: this.$t("breadcrumb.recipe.gallery"), params: { id: this.recipe.id, name: this.recipe.name } })
            if (this.$route.name == 'editRecipe')
              items.push({ target: 'editRecipe', title: this.$t("breadcrumb.recipe.edit"), params: { id: this.recipe.id, name: this.recipe.name } })
          }
          return items

        case 'recipes':
          items.push(bciRecipes)
          return items

        case 'myRecipes':
          items.push(bciRecipesMy)
          return items

        case 'search':
          items.push(bciSearch)
          return items

        case 'userRecipes':
          items.push({ target: 'userRecipes', title: this.$t("breadcrumb.recipes.users", { user: this.$route.params.name }), params: { id: this.$route.params.id, name: this.$route.params.name } })
          return items

        case 'writeRecipe':
          items.push(bciWriteRecipe)
          return items

      }

      // fallback: return no breadcrumb
      return [ ]

    }
  }
})
Vue.component('rc-navbar', {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#rc-navbar-template',
  data: function() {
    return { }
  },
  methods: {
    onSearchInput: function() {
      if (app.$route.name != 'search')
        app.$router.push({name: 'search'})
    }
  }
})
const SearchRecipe = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#rc-search-template',
  methods: {
    onClick: function() {
      if (this.page.search.filter.global.length >= 3)
        app.debouncedSearch()
      else
        app.$router.push({ name: 'recipes' })
    },
    onSearchItemClicked: function(index, id, name) {
      app.$router.push({ name: 'recipe', params: { id: id, name: name } })
    }
  }
}
const RecipesCreator = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#recipe-write-template',
  data: function() {
    return {
      form: {
        completed: false,
        validationFailed: false,
      },
      progress: {
        preparing: false,
        sending: false,
        gotresponse: false,
        responseSuccess: false,
        responseId: 0,
        response: {
          code: 1,
          message: ''
        }
      }
    }
  },
  methods: {
    onSubmit: function(e) {
      e.preventDefault()
      if (this.form.completed) {
        $('#new-recipe-modal').modal('show')
        return
      }
      var missinginfo = false
      if ( this.page.currentRecipe.preparation.ingredients.length == 0
        || this.page.currentRecipe.preparation.steps.length == 0)
        missinginfo = true
      $('.needs-validation').find('input,select,textarea').each(function () {
        // check element validity and change class
        $(this).removeClass('is-valid is-invalid')
        var result = this.checkValidity()
        $(this).addClass(result ? 'is-valid' : 'is-invalid')
        if (!result)
          missinginfo = true
       });
       this.form.validationFailed = missinginfo
       if (!missinginfo) {
         this.progress.preparing = true
         $('#new-recipe-modal').modal('show')
         var formdata = new FormData($('#new-recipe-form')[0])
         this.progress.preparing = false
         this.progress.sending = true
         const parent = this
         postFormData(app.$route.path, formdata, function(data) {
           parent.progress.gotresponse = true
           if (data.success) {
             parent.form.completed = true
             parent.progress.responseSuccess = true
             parent.progress.responseId = data.recipeId
           } else {
             parent.progress.responseSuccess = false
           }
           parent.progress.response.code = data.code
           parent.progress.response.message = data.message
           parent.progress.sending = false
         })
       }
    },
    onPictureAddBtnClick: function() {
      var i = this.page.currentRecipe.pictures.length
      this.page.currentRecipe.pictures.push({ file: null })
      const parent = this
      setTimeout(function() {
        // wait for upload button to be created
        var i = parent.page.currentRecipe.pictures.length - 1
        $('#file-' + i).click()
      }, 100)
    },
    onPictureDelBtnClick: function(i) {
      this.page.currentRecipe.pictures.splice(i, 1)
    },
    onPictureInput: function(i) {
      if (!window.FileReader)
        return
      if (!this.page.currentRecipe.pictures[i].file) {
        $('#picture-image-' + i).css("content", "none")
        return
      } else {
        if (/^image/.test(this.page.currentRecipe.pictures[i].file.type)) {
          var reader = new FileReader()
          reader.readAsDataURL(this.page.currentRecipe.pictures[i].file)
          reader.onloadend = function() {
            $('#picture-image-' + i).css("content", "url(" + this.result + ")")
          }
        }
      }
    },
    onIngredientDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.ingredients.splice(i, 1)
    },
    onIngredientAddBtnClick: function() {
      this.page.currentRecipe.preparation.ingredients.push({ amount: '', unit: '', description: '' })
    },
    onStepDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.steps.splice(i, 1)
    },
    onStepAddBtnClick: function() {
      this.page.currentRecipe.preparation.steps.push({ index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } })
    },
    onNewRecipeBtnClick: function(i) {
      initEmptyRecipe(app)
      this.form.completed = false
      this.form.validationFailed = false
      this.progress.preparing = false
      this.progress.sending = false
      this.progress.gotresponse = false
      this.progress.responseSuccess = false
      this.progress.responseId = 0
      this.progress.response.code = 1
      this.progress.response.message = ''
      $('.needs-validation').find('input,select,textarea').each(function () {
        $(this).removeClass('is-valid is-invalid')
       });
      $('#new-recipe-modal').modal('hide')
    },
    onGotoRecipeBtnClick: function(i) {
      console.log('@onGotoRecipeBtnClick')
      router.push({ name: 'recipe', params: { id: this.progress.responseId, name: this.page.currentRecipe.name } })
    }
  }
}
const RecipeEditor = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#recipe-write-template',
  data: function() {
    return {
      form: {
        completed: false,
        validationFailed: false,
      },
      progress: {
        preparing: false,
        sending: false,
        gotresponse: false,
        responseSuccess: false,
        responseId: 0,
        response: {
          code: 1,
          message: ''
        }
      }
    }
  },
  methods: {
    onSubmit: function(e) {
      e.preventDefault()
      var missinginfo = false
      $('.needs-validation').find('input,select,textarea').each(function () {
        // check element validity and change class
        $(this).removeClass('is-valid is-invalid')
        var result = this.checkValidity()
        $(this).addClass(result ? 'is-valid' : 'is-invalid')
        if (!result)
          missinginfo = true
       });
       this.form.validationFailed = missinginfo
       if (!missinginfo) {
         this.progress.preparing = true
         $('#new-recipe-modal').modal('show')
         var formdata = new FormData($('#new-recipe-form')[0])
         this.progress.preparing = false
         this.progress.sending = true
         const parent = this
         postFormData(app.$route.path, formdata, function(data) {
           parent.progress.gotresponse = true
           if (data.success) {
             parent.form.completed = true
             parent.progress.responseSuccess = true
             parent.progress.responseId = data.recipeId
           } else {
             parent.progress.responseSuccess = false
           }
           parent.progress.response.code = data.code
           parent.progress.response.message = data.message
           parent.progress.sending = false
         })
       }
    },
    onIngredientDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.ingredients.splice(i, 1)
      if (this.page.currentRecipe.preparation.ingredients.length == 0) {
        for (i=0; i<3; i++)
          this.page.currentRecipe.preparation.ingredients.push({ amount: '', unit: '', description: '' })
      }
    },
    onIngredientAddBtnClick: function() {
      for (i=0; i<3; i++)
        this.page.currentRecipe.preparation.ingredients.push({ amount: '', unit: '', description: '' })
    },
    onStepDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.steps.splice(i, 1)
      if (this.page.currentRecipe.preparation.steps.length == 0) {
        this.page.currentRecipe.preparation.steps.push({ index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } })
      }
    },
    onStepAddBtnClick: function() {
      this.page.currentRecipe.preparation.steps.push({ index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } })
    },
    onNewRecipeBtnClick: function(i) {
      return
    },
    onGotoRecipeBtnClick: function(i) {
      console.log('@onGotoRecipeBtnClick')
      router.push({ name: 'recipe', params: { id: this.page.currentRecipe.id, name: this.page.currentRecipe.name } })
    }
  }
}
const router = new VueRouter({
  mode: 'history',
  routes: [
    { name: 'account', path: '/profile', children: [
      { name: 'settings', path: 'settings' }
    ]},
    { name: 'logout', path: '/logout', component: Logout },
    { name: 'admin', path: '/admin', children: [
      { name: 'configuration', path: 'configuration' },
      { name: 'cronjobs', path: 'cronjobs' },
      { name: 'translations', path: 'translations' },
      { name: 'logs', path: 'logs' },
      { name: 'users', path: 'users' }
    ]},
    { name: 'home', path: '/home', alias: '/', component: Home },
    { name: 'login', path: '/login', component: Login },
    { name: 'random', path: '/random/:id?' },
    { name: 'recipe', path: '/recipe/:id(.+)-:name([^/]*)', component: Recipe },
    { name: 'gallery', path: '/recipe/:id(.+)-:name([^/]*)/gallery', component: Recipe },
    { name: 'editRecipe', path: '/recipe/:id(.+)-:name([^/]*)/edit', component: RecipeEditor },
    { name: 'recipes', path: '/recipes', component: RecipesList, children: [
      { name: 'recipePage', path: ':page' },
      { name: 'myRecipes', path: 'my' },
      { name: 'userRecipes', path: 'user/:id(.+)-:name([^/]*)' }
      ]
    },
    { name: 'search', path: '/search', component: SearchRecipe },
    { name: 'user', path: '/user/:id(.+)-:name([^/]*)', children: [

    ]},
    { name: 'writeRecipe', path: '/write', component: RecipesCreator },
  ]
})

// Vue.config.productionTip = false
Vue.use(VueResource)
Vue.http.options.root = '/api/'

var app = new Vue({
  delimiters: ['${', '}'],
  el: '#vue-app',
  data: CommonData,
  router,
  i18n,
  created: function() {
    window.addEventListener("resize", this.onResize)
    this.debouncedSearch = _.debounce(this.getSearchResults, 500)
    var lgspy = $('#reactive-size-spy-lg');
    CommonData.page.sidebar.visible = (lgspy.css("display") == "block")
    CommonData.page.sidebar.initialVisible = (lgspy.css("display") == "block")
    if (CommonData.page.sidebar.visible == false) {
      $('#sidebar-main').css("display", "none")
      $('#sidebar-main').prop("aria-hidden", "true")
    }
  },
  destroyed: function() {
    window.removeEventListener("resize", this.onResize)
  },
  mounted: function() {
    if (!CommonData.user.loggedIn)
      this.$router.push({name: 'login'})
    refreshPageData(this.$route.path, this)
  },
  computed: {
    title: function() {
      switch(this.$route.name) {
        case 'recipe':
          if (this.page.currentRecipe.ownerId > 0)
            return this.$t('pages.recipe.titleWithUser', { recipe: this.page.currentRecipe.name, user: this.page.currentRecipe.ownerName })
          return this.$t('pages.recipe.title', { recipe: this.page.currentRecipe.name })
        case 'userRecipes':
          return this.$t('pages.userRecipes.title', { name: this.$route.params.name })
      }
      return this.$t('pages.' + this.$route.name + '.title')
    },
    subtitle: function() {
      return this.$t('pages.' + this.$route.name + '.subtitle')
    }
  },
  methods: {
    onResize: function() {
      var smspy = $('#reactive-size-spy-sm');
      var lgspy = $('#reactive-size-spy-lg');
      if (this.page.sidebar.visible != (lgspy.css("display") == "block")) {
        this.$set(this.page.sidebar, 'visible', (lgspy.css("display") == "block"))
        if (this.page.sidebar.visible == false) {
          $('#sidebar-main').css("display", "none")
          $('#sidebar-main').prop("aria-hidden", "true")
        } else {
          $('#sidebar-main').css("display", "")
          $('#sidebar-main').prop("aria-hidden", "")
        }
      }
    },
    onClick: function(e) {
      this.$emit('click', this.subject ? this.subject : this.title)
    },
    getSearchResults: function() {
      resetSearchData(this)
      postPageData(this.$route.path, {
        search: {
          phrase: this.page.search.filter.global
        }
      }, function(data) {
        updateProps(data, app)
      })
    }
  },
  watch: {
    'page.search.filter.global': function() {
      if (this.page.search.filter.global.length >= 3)
        this.debouncedSearch()

    }
  }
})

router.beforeEach((to, from, next) => {
  console.log('beforeEach', to, from)
  if (app) {
    if (to.name == 'login' && app.user.loggedIn)
      next(false)
    if (to.name != 'login' && !app.user.loggedIn)
      next({ name: 'login' })
    if (to.name == 'home' || to.name == 'writeRecipe' || to.name == 'search') {
      resetCustomPageData(app, to.name)
    } else if (
      !((to.name == 'recipe' || to.name == 'editRecipe' || to.name == 'gallery') &&
        (from.name == 'recipe' || from.name == 'editRecipe' || from.name == 'gallery'))
    ) {
      resetPageData(app)
      refreshPageData(to.path)
    }
  }
  next()
})

router.afterEach((to, from) => {
  console.log('afterEach', to, from)
  if (to.name == 'logout') {
    Vue.http.post('logout')
      .then(response => response.json())
      .then(() => {
        window.location = '/';
      })
  }
})

function resetPageData(app) {
  app.$set(app.page.contentData, 'actions', [])
  app.$set(app.page.contentData, 'breadcrumbs', [])
  app.$set(app.page.contentData, 'filters', [])
  app.$set(app.page.contentData, 'hasActions', false)
  app.$set(app.page.contentData, 'hasFilters', false)
  app.$set(app.page.currentRecipe, 'id', 0)
  app.$set(app.page.currentRecipe, 'name', '')
  app.$set(app.page.currentRecipe, 'created', 0)
  app.$set(app.page.currentRecipe, 'description', '')
  app.$set(app.page.currentRecipe, 'eaterCount', 4)
  app.$set(app.page.currentRecipe, 'eaterCountCalc', 4)
  app.$set(app.page.currentRecipe, 'ownerId', 0)
  app.$set(app.page.currentRecipe, 'ownerName', '')
  app.$set(app.page.currentRecipe, 'published', false)
  app.$set(app.page.currentRecipe.source, 'description', '')
  app.$set(app.page.currentRecipe.source, 'url', '')
  app.$set(app.page.currentRecipe, 'pictures', [])
  app.$set(app.page.currentRecipe.preparation, 'ingredients', [])
  app.$set(app.page.currentRecipe.preparation, 'steps', [])
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'cooking', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'preparing', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'rest', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'total', 0)
  app.$set(app.page.currentRecipe.socials, 'cookedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'ratedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'ratedSum', 0)
  app.$set(app.page.currentRecipe.socials, 'viewCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'votedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'votedSum', 0)
  app.$set(app.page.currentRecipe.socials, 'votedAvg0', 0)
  app.$set(app.page.currentRecipe.socials, 'votedAvg1', 0)
  app.$set(app.page.currentRecipe, 'tags', [])
  app.$set(app.page.customContent, 'count', 0)
  app.$set(app.page.customContent, 'page', 0)
  app.$set(app.page.customContent, 'pages', 0)
  app.$set(app.page.customContent, 'records', [])
  app.$set(app.page.self.currentVote, 'cooked', -1)
  app.$set(app.page.self.currentVote, 'rating', -1)
  app.$set(app.page.self.currentVote, 'voting', -1)
  app.$set(app.page.self, 'hasVoted', false)
  app.$set(app.page.self.lastVote, 'id', 0)
  app.$set(app.page.self.lastVote, 'userId', 0)
  app.$set(app.page.self.lastVote, 'user', '')
  app.$set(app.page.self.lastVote, 'time', '')
  app.$set(app.page.self.lastVote, 'comment', '')
  app.$set(app.page.self.lastVote, 'cooked', false)
  app.$set(app.page.self.lastVote, 'voting', 0)
  app.$set(app.page.self.lastVote, 'rating', 0)
  app.$set(app.page.self, 'visitCount', 0)
  app.$set(app.page.self, 'voteCount', 0)
}

function resetCustomPageData(app, route) {
  switch (route) {
    case 'home':
    case 'writeRecipe':
      app.$set(app.page.contentData, 'actions', [])
      app.$set(app.page.contentData, 'breadcrumbs', [])
      app.$set(app.page.contentData, 'filters', [])
      app.$set(app.page.contentData, 'hasActions', false)
      app.$set(app.page.contentData, 'hasFilters', false)
      app.$set(app.page.currentRecipe, 'id', 0)
      app.$set(app.page.customContent, 'count', 0)
      app.$set(app.page.customContent, 'page', 0)
      app.$set(app.page.customContent, 'pages', 0)
      app.$set(app.page.customContent, 'records', [])
      if (route == 'writeRecipe')
        initEmptyRecipe(app)
      break;
  }
}

function resetSearchData(app) {
  app.$set(app.page.search.records, 'total', 0)
  app.$set(app.page.search.records, 'numpages', 0)
  app.$set(app.page.search.records, 'page', 0)
  app.$set(app.page.search, 'results', [])
}

function initEmptyRecipe(app) {
  app.$set(app.page.currentRecipe, 'id', 0)
  app.$set(app.page.currentRecipe, 'name', '')
  app.$set(app.page.currentRecipe, 'created', 0)
  app.$set(app.page.currentRecipe, 'description', '')
  app.$set(app.page.currentRecipe, 'eaterCount', 4)
  app.$set(app.page.currentRecipe, 'eaterCountCalc', 4)
  app.$set(app.page.currentRecipe, 'ownerId', 0)
  app.$set(app.page.currentRecipe, 'ownerName', '')
  app.$set(app.page.currentRecipe, 'published', false)
  app.$set(app.page.currentRecipe.source, 'description', '')
  app.$set(app.page.currentRecipe.source, 'url', '')
  app.$set(app.page.currentRecipe, 'pictures', [])
  app.$set(app.page.currentRecipe.preparation, 'ingredients', [])
  app.$set(app.page.currentRecipe.preparation, 'steps', [])
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'cooking', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'preparing', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'rest', 0)
  app.$set(app.page.currentRecipe.preparation.timeConsumed, 'total', 0)
  app.$set(app.page.currentRecipe.socials, 'cookedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'ratedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'ratedSum', 0)
  app.$set(app.page.currentRecipe.socials, 'viewCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'votedCounter', 0)
  app.$set(app.page.currentRecipe.socials, 'votedSum', 0)
  app.$set(app.page.currentRecipe.socials, 'votedAvg0', 0)
  app.$set(app.page.currentRecipe.socials, 'votedAvg1', 0)
  app.$set(app.page.currentRecipe, 'tags', [])
}

function refreshPageData(path, appparam = false) {
  if (appparam !== false && !app)
    app = appparam
  if (!app)
    return;
  app.$set(app.page, 'loading', true)
  var m0 = performance.now()
  Vue.http.get('page-data?' + encodeURI(path))
    .then(response => response.json())
    .then((data) => {
      //console.log(data)
      if (data.forward && data.forward !== false) {
        if (data.forward.ext) {
          location.href = data.forward.extUrl;
          return;
        }
        updateProps(data, app)
        router.push({ name: data.forward.route, params: data.forward.params })
      }
      else if (data.page) {
        updateProps(data, app)
      }
      if (path == '/write')
        initEmptyRecipe(app)
      app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
      app.$set(app.page, 'loading',false)
    })
}

function postPageData(path, data, callback, updateOnSuccess = false) {
  if (!app)
    return;
  app.$set(app.page, 'updating', true)
  var m0 = performance.now()
  $.ajax({
    url: '/api/page-data?' + encodeURI(path),
    method: 'POST',
    data: data
  })
  .done(function(data) {
    if (updateOnSuccess == true && data.success == true)
      updateProps(data, app)
    app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
    app.$set(app.page, 'updating',false)
    callback(data);
  })
  .fail(function(jqXHR, textStatus) {
    console.log(jqXHR, textStatus)
    app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
    app.$set(app.page, 'updating',false)
  })
}

function postFormData(path, data, callback, updateOnSuccess = false) {
  if (!app)
    return;
  app.$set(app.page, 'updating', true)
  var m0 = performance.now()
  $.ajax({
    url: '/api/page-data?' + encodeURI(path),
    method: 'POST',
    contentType: false,
    processData: false,
    cache: false,
    data: data
  })
  .done(function(data) {
    if (updateOnSuccess == true && data.success == true)
      updateProps(data, app)
    app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
    app.$set(app.page, 'updating',false)
    callback(data);
  })
  .fail(function(jqXHR, textStatus) {
    console.log(jqXHR, textStatus)
    app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
    app.$set(app.page, 'updating',false)
    callback({ success: false, code: -1, message: textStatus })
  })
}

function formatMillis(duration) {
  if (duration < 900)
    return duration.toFixed(0) + ' ms';
  return (duration / 1000).toFixed(2) + ' s'
}

function updateProps(data, prop) {
  for (key in data) {
    //console.log(key + ': ' +  data[key])
    if (prop[key] === undefined || typeof data[key] != typeof prop[key])
      createProp(prop, key, data[key])
    else {
      if (typeof data[key] === 'object')
        updateProps(data[key], prop[key])
      else if (Array.isArray(data[key]) && data[key].length != prop[key].length)
        createProp(prop, key, data[key])
      else if (Array.isArray(data[key]) && data[key].length == prop[key].length)
        updateProps(data[key], prop[key])
      else {
        try {
          app.$set(prop, key, data[key])
        } catch(e) {
          console.log('EX updateProps', e)
        }
      }
    }
  }
}

function createProp(prop, key, fromobj) {
  try {
    app.$set(prop, key, fromobj)
  } catch(e) {
    console.log('EX updateProps', e)
  }
}