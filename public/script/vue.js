
var User = {
  loggedIn: false,
  isAdmin: false
}

var app = new Vue({
  delimiters: ['${', '}'],
  el: '#vue-app',
  data: {
    config: {
      maintenanceEnabled: false,
      login: {
        defaultEnabled: true,
        oauth2Enabled: false
      }
    },
    user: User
  }
})
