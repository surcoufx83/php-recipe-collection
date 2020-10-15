
const router = new VueRouter({
  mode: 'history',
  routes: [
    { name: 'account', path: '/profile', children: [
      { name: 'logout', path: 'logout' },
      { name: 'settings', path: 'settings' }
    ]},
    { name: 'admin', path: '/admin', children: [
      { name: 'cronjobs', path: 'cronjobs' },
      { name: 'translations', path: 'translations' },
      { name: 'logs', path: 'logs' },
      { name: 'users', path: 'users' }
    ]},
    { name: 'home', path: '/home', alias: '/', component: Home },
    { name: 'random', path: '/random/:id?' },
    { name: 'recipe', path: '/recipe/:id(.+)-:name([^/]*)', component: Recipe, children: [
      { name: 'editRecipe', path: 'edit', component: Recipe }
    ]},
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
