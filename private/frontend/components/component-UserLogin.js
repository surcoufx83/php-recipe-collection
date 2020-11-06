const UserLogin = {
  delimiters: ['${', '}'],
  props: ['page', 'user', 'config'],
  template: '#login-template',
  data: function() {
    return {
      username: '',
      password: '',
      keepSession: false,
      submitted: false,
      submitting: false
    }
  },
  computed: {
    usernamestate: function() {
      return (!this.submitted || (this.username !== '' && this.username.length > 3)) && this.username.length <= 32
    },
    passwordstate: function() {
      return !this.submitted || (this.password !== '' && this.password.length > 4)
    }
  },
  methods: {
    onLoginSubmit: function(e) {
      this.submitted = true
      e.preventDefault()
      if (this.username === '' || this.username.length < 3 || this.username.length > 32)
        return false
      if (this.password === '' || this.password.length < 5)
        return false

      app.$set(app.page, 'updating', true)
      var m0 = performance.now()
      $.ajax({
        url: '/api/login',
        method: 'POST',
        data: {
          userid: this.username,
          userpwd: this.password,
          keepsession: this.keepSession
        }
      })
      .done(function(data) {
        console.log(data)
        if (data.success == true) {
          updateProps(data, app)
        } else {
          app.$set(app.page.modals.failedModal, 'message', app.$t(data.i18nmessage))
          app.$set(app.page.modals.failedModal, 'code', data.code)
          $('#action-failed-modal').modal('show')
        }
        app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
        app.$set(app.page, 'updating', false)
      })
      .fail(function(jqXHR, textStatus) {
        console.log(jqXHR, textStatus)
        app.$set(app.page, 'loadingTime', formatMillis(performance.now() - m0))
        app.$set(app.page, 'updating', false)
        app.$set(app.page.modals.failedModal, 'message', jqXHR + textStatus)
        app.$set(app.page.modals.failedModal, 'code', -1)
        $('#action-failed-modal').modal('show')
      })

    }
  }
}
