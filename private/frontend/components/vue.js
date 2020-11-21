
// Vue.config.productionTip = false
Vue.use(VueResource)
Vue.http.options.root = '/api/'

var app = new Vue({
  i18n,
  router,
  delimiters: ['${', '}'],
  el: '#vue-app',
  data: CommonData,
  created: function() {
    window.addEventListener("resize", this.onResize)
    this.debouncedSearch = _.debounce(this.getSearchResults, 500)
    var lgspy = $('#reactive-size-spy-lg');
    CommonData.page.sidebar.visible = (lgspy.css("display") == "block" && CommonData.user.loggedIn)
    CommonData.page.sidebar.initialVisible = (lgspy.css("display") == "block" && CommonData.user.loggedIn)
    if (CommonData.page.sidebar.visible == false) {
      $('#sidebar-main').css("display", "none")
      $('#sidebar-main').prop("aria-hidden", "true")
    }
  },
  destroyed: function() {
    window.removeEventListener("resize", this.onResize)
  },
  mounted: function() {
    if (!CommonData.user.loggedIn && this.$route.name !== 'login')
      this.$router.push({name: 'login'})
    else if (CommonData.user.loggedIn && this.$route.name === 'login')
      this.$router.push({name: 'home'})
    refreshPageData(this.$route.path, this)
  },
  computed: {
    title: function() {
      switch(this.$router.currentRoute.matched[0].name) {
        case 'account':
          return this.$t('pages.account.title', { name: this.user.meta.fn })
        case 'gallery':
        case 'recipe':
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
      var lgspy = $('#reactive-size-spy-lg');
      if (this.page.sidebar.visible != (lgspy.css("display") == "block")) {
        this.$set(this.page.sidebar, 'visible', (lgspy.css("display") == "block" && CommonData.user.loggedIn))
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
      if (this.$route.name != 'search')
        this.$router.push({ name: 'search' });
      resetSearchData(this)
      this.$set(this.page.search, 'hasSearchCompleted', false)
      this.$set(this.page.search, 'isSearching', true)
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
    },
    'user.isAdmin': function() {
      location.reload()
    },
    'user.loggedIn': function() {
      location.reload()
    }
  }
})

router.beforeEach((to, from, next) => {
  // console.log('beforeEach', to, from)
  if (app) {
    if (to.name == 'login' && app.user.loggedIn)
      next(false)
    if (to.name != 'login' && !app.user.loggedIn)
      next({ name: 'login' })
    if (to.name == 'writeRecipe' || to.name == 'search') {
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
  // console.log('afterEach', to, from)
  if (to.name == 'logout') {
    Vue.http.post('logout')
      .then(response => response.json())
      .then(() => {
        window.location = '/';
      })
  } else if (to.name == 'login') {
    if (app.user.loggedIn)
      window.location = '/';
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
