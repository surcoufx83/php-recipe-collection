
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
        }
      }
    })
    isAuthenticated = app.user.loggedIn
  })

router.beforeEach((to, from, next) => {
  if (to.name != 'logout')
    refreshPageData(to.path)
  next()
  // ...
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
      if (data.forward) {
        if (data.forward.ext) {
          location.href = data.forward.extUrl;
          return;
        }
        router.push({ name: data.forward.route, params: data.forward.params })
      }
      else if (data.page) {
        if (data.page.contentData) {
          if (data.page.contentData.breadcrumbs)
            app.$set(app.page.contentData, 'breadcrumbs', [])
        }
        updateProps(data, app)
      }
      app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
      app.$set(app.page, 'loading',false)
    })
  }

  function postPageData(path, data, callback) {
    if (!app)
      return;
    app.$set(app.page, 'loading', true)
    var m0 = performance.now()
    $.ajax({
      url: '/api/page-data?' + encodeURI(path),
      method: 'POST',
      data: data
    })
    .done(function(data) {
      app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
      app.$set(app.page, 'loading',false)
      callback(data);
    })
    .fail(function(jqXHR, textStatus) {
      console.log(jqXHR, textStatus)
      app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
      app.$set(app.page, 'loading',false)
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
        if (typeof data[key] === 'object' || Array.isArray(data[key]))
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
