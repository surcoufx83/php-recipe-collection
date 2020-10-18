
var app;
var isAuthenticated;

Vue.config.productionTip = false
Vue.use(VueResource)
Vue.http.options.root = '/api/'

Vue.http.get('common-data')
  .then(response => response.json())
  .then((data) => {
    app = new Vue({
      delimiters: ['${', '}'],
      el: '#vue-app',
      router,
      data: data,
      created: function() {
        window.addEventListener("resize", this.onResize);
      },
      destroyed: function() {
        window.removeEventListener("resize", this.onResize);
      },
      mounted: function() {
        var smspy = $('#reactive-size-spy-sm');
        var lgspy = $('#reactive-size-spy-lg');
        this.$set(this.page.sidebar, 'visible', (lgspy.css("display") == "block"))
        this.$set(this.page.sidebar, 'initialVisible', (lgspy.css("display") == "block"))
        if (this.page.sidebar.visible == false) {
          $('#sidebar-main').css("display", "none")
          $('#sidebar-main').prop("aria-hidden", "true")
        }
        refreshPageData(this.$route.path, this)
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
          console.log('@onClick', e, this.target)
          this.$emit('click', this.subject ? this.subject : this.title)
        }
      }
    })
    isAuthenticated = app.user.loggedIn
  })

router.beforeEach((to, from, next) => {
  if (app) {
    if (
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
  if (to.name == 'logout') {
    Vue.http.post('logout')
      .then(response => response.json())
      .then((data) => {
        window.location = '/';
      })
  }
})

function resetPageData(app) {
  app.$set(app.page.contentData, 'actions', [])
  app.$set(app.page.contentData, 'breadcrumbs', [])
  app.$set(app.page.contentData, 'filters', [])
  app.$set(app.page.contentData, 'title', '')
  app.$set(app.page.contentData, 'titleDescription', '')
  app.$set(app.page.contentData, 'hasActions', false)
  app.$set(app.page.contentData, 'hasFilters', false)
  app.$set(app.page, 'currentRecipe', {})
  app.$set(app.page, 'currentUser', {})
  app.$set(app.page, 'customContent', false)
  app.$set(app.page, 'self', {
    currentVote: { cooked: -1, rating: -1, voting: -1},
    hasVoted: false,
    lastVote: { id: '', userId: '', user: '', time: '', comment: '', cooked: '', voting: '', rating: '', formatted: { time: ''}},
    visitCount: 0,
    voteCount: 0
  })
}

function initEmptyRecipe(app) {
  app.$set(app.page.currentRecipe, 'id', 0)
  app.$set(app.page.currentRecipe, 'name', '')
  app.$set(app.page.currentRecipe, 'created', false)
  app.$set(app.page.currentRecipe, 'description', '')
  app.$set(app.page.currentRecipe, 'eaterCount', 4)
  app.$set(app.page.currentRecipe, 'eaterCountCalc', 4)
  app.$set(app.page.currentRecipe, 'ownerId', 0)
  app.$set(app.page.currentRecipe, 'ownerName', '')
  app.$set(app.page.currentRecipe, 'published', false)
  app.$set(app.page.currentRecipe, 'source', { description: '', url: '' })
  app.$set(app.page.currentRecipe, 'formatted', { created: '', published: '' })
  app.$set(app.page.currentRecipe, 'pictures', [
    { file: null },
    { file: null },
    { file: null }
  ])
  app.$set(app.page.currentRecipe, 'preparation', {
    ingredients: [
      { id: 0, unitId: 0, unit: { id: 0, name: '' }, quantity: '', quantityCalc: '', description: ''},
      { id: 0, unitId: 0, unit: { id: 0, name: '' }, quantity: '', quantityCalc: '', description: ''},
      { id: 0, unitId: 0, unit: { id: 0, name: '' }, quantity: '', quantityCalc: '', description: ''},
      { id: 0, unitId: 0, unit: { id: 0, name: '' }, quantity: '', quantityCalc: '', description: ''},
      { id: 0, unitId: 0, unit: { id: 0, name: '' }, quantity: '', quantityCalc: '', description: ''}
    ],
    steps: [
      { index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } },
      { index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } },
      { index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } }
    ],
    timeConsumed: {
      cooking: 0,
      preparing: 0,
      rest: 0,
      total: 0,
      unit: 'minutes',
      formatted: {
        cooking: { valueStr: '', timeStr: '', timeStr2: '' },
        preparing: { valueStr: '', timeStr: '', timeStr2: '' },
        rest: { valueStr: '', timeStr: '', timeStr2: '' },
        total: { valueStr: '', timeStr: '', timeStr2: '' }
      }
    }
  })
  app.$set(app.page.currentRecipe, 'socials', {
    cookedCounter: 0,
    ratedCounter: 0,
    ratedSum: 0,
    viewCounter: 0,
    votedCounter: 0,
    votedSum: 0,
    votedAvg1: '0.0',
    votedAvg0: '0'
  })
  app.$set(app.page.currentRecipe, 'tags', { items: [], votes: [] })
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
