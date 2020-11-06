Vue.component('rc-navbar', {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#rc-navbar-template',
  data: function() {
    return { }
  },
  methods: {
    onSearchInput: function() {
      app.debouncedSearch()
    },
    onSearchInputFocused: function() {
      this.page.search.filter.hasFocus = true;
    },
    onSearchInputBlurred: function() {
      this.page.search.filter.hasFocus = false;
    }
  }
})
