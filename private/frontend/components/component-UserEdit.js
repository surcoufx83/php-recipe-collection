
const UserEdit = {
  delimiters: ['${', '}'],
  props: ['config', 'page', 'user'],
  template: '#useredit-template',
  data: function() {
    return {
      mailupdate: {
        failed: false,
        message: ''
      }
    }
  },
  methods: {
    onEmailInput: _.debounce(function(e) {
      if (e !== '' && !validEmail(e))
        return
      this.mailupdate.failed = false
      const me = this
      postPageData(app.$route.path, { 'update': { 'email': e } }, function (data) {
        if (!data.success) {
          if (data.code == 401) {
            me.mailupdate.message = app.$t(data.i18nmessage)
            me.mailupdate.failed = true
          }
        }
      })
    }, 500),
    onFirstnameInput: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'firstname': e } })
    }, 500),
    onLastnameInput: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'lastname': e } })
    }, 500),
    validEmail: function(e) {
      return (e === '' || validEmail(e))
    }
  }
}
