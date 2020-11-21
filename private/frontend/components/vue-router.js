const router = new VueRouter({
  mode: 'history',
  routes: [
    { name: 'account', path: '/profile', component: UserProfile, children: [
      { name: 'notifications', path: 'notifications' },
      { name: 'settings', path: 'settings' },
      { name: 'subscriptions', path: 'subscriptions' },
    ]},
    { name: 'admin', path: '/admin', children: [
      { name: 'configuration', path: 'configuration' },
      { name: 'cronjobs', path: 'cronjobs' },
      { name: 'translations', path: 'translations' },
      { name: 'logs', path: 'logs' },
      { name: 'users', path: 'users' }
    ]},
    { name: 'editRecipe', path: '/recipe/:id(.+)-:name([^/]*)/edit', component: RecipeEditor },
    { name: 'gallery', path: '/recipe/:id(.+)-:name([^/]*)/gallery', component: RecipeGallery },
    { name: 'home', path: '/home', alias: '/', component: Home },
    { name: 'login', path: '/login', component: UserLogin },
    { name: 'logout', path: '/logout', component: Logout },
    { name: 'lostPwd', path: '/pwlost' },
    { name: 'random', path: '/random/:id?' },
    { name: 'recipe', path: '/recipe/:id(.+)-:name([^/]*)', component: Recipe },
    { name: 'recipes', path: '/recipes', component: RecipesList, children: [
      { name: 'myRecipes', path: 'my' },
      { name: 'userRecipes', path: 'user/:id(.+)-:name([^/]*)' }
    ]},
    { name: 'search', path: '/search', component: SearchRecipe },
    { name: 'user', path: '/user/:id(.+)-:name([^/]*)'},
    { name: 'writeRecipe', path: '/write', component: RecipesCreator },
  ]
})
