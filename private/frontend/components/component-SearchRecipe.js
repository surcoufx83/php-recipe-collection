const SearchRecipe = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#rc-search-template',
  computed: {
  },
  methods: {
    onClick: function() {
      if (this.page.search.filter.global.length >= 3)
        app.debouncedSearch()
      else
        app.$router.push({ name: 'recipes' })
    },
    onSearchItemClicked: function(index, id, name) {
      app.$router.push({ name: 'recipe', params: { id: id, name: name } })
    },
    published: function(recipe) {
      return moment(recipe.published, moment.ISO_8601).format(this.user.customSettings.formats.date.long)
    }
  }
}
