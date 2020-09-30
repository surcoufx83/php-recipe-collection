
var app;
var isAuthenticated;

Vue.config.productionTip = false
Vue.use(VueResource)
Vue.http.options.root = '/api/'

Vue.component('breadcrumb', {
  props: ['target', 'title'],
  template:
    '<li class="breadcrumbs-item">' +
    '<router-link :to="{ name: target }">{{title}}' +
    '</router-link></li>'
})

const router = new VueRouter({
  routes: [
    { name: 'admin', path: '/admin', children: [
      { name: 'cronjobs', path: 'cronjobs' },
      { name: 'translations', path: 'translations' },
      { name: 'logs', path: 'logs' },
      { name: 'users', path: 'users' },
    ]},
    { name: 'home', path: '/home', alias: '/' },
    { name: 'login', path: '/login' },
    { name: 'logout', path: '/logout' },
    { name: 'my', path: '/my', children: [
      { name: 'recipes', path: 'recipes' },
      { name: 'settings', path: 'settings' },
    ]},
    { name: 'newRecipe', path: '/recipe/new' },
    { name: 'random', path: '/random' },
    { name: 'recipe', path: '/recipe/:id-:name', children: [
      { name: 'editRecipe', path: 'edit' },
    ]},
    { name: 'search', path: '/search' }
  ]
})

Vue.http.get('common-data')
  .then(response => response.json())
  .then((data) => {
    app = new Vue({
      delimiters: ['${', '}'],
      el: '#vue-app',
      router,
      data: data
    })
    isAuthenticated = app.user.loggedIn;
  })

router.afterEach((to, from) => {
  if (to.name == 'logout') {
    Vue.http.post('logout')
      .then(response => response.json())
      .then((data) => {
        window.location = '/';
      })
  } else {
    Vue.http.get('page-data?' + encodeURI(router.currentRoute.path))
      .then(response => response.json())
      .then((data) => {
        console.log(data);
        app.page.contentData.breadcrumbs.splice(0)
        if (data.page) {
          if (data.page.contentData) {
            if (data.page.contentData.breadcrumbs) {
              for (i=0; i<data.page.contentData.breadcrumbs.length; i++) {
                app.$set(app.page.contentData.breadcrumbs, i, data.page.contentData.breadcrumbs[i])
              }
            }
          }
        }
      })
  }
})
