
const UserContactSettings = {
  delimiters: ['${', '}'],
  props: ['config', 'page', 'user'],
  template: '#usercontact-template',
  methods: {
    onU2uMsgConsentChange: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'consent': { 'user2me': { 'message': e } } } })
    }, 500),
    onU2uMailConsentChange: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'consent': { 'user2me': { 'mail': e } } } })
    }, 500),
    onU2uExposeConsentChange: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'consent': { 'user2me': { 'expose': e } } } })
    }, 500),
    onS2uMsgConsentChange: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'consent': { 'sys2me': { 'message': e } } } })
    }, 500),
    onS2uMailConsentChange: _.debounce(function(e) {
      postPageData(app.$route.path, { 'update': { 'consent': { 'sys2me': { 'mail': e } } } })
    }, 500)
  }
}
