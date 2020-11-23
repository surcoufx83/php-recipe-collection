
const UserProfile = {
  delimiters: ['${', '}'],
  props: ['config', 'page', 'user'],
  template: '#userprofile-template',
  data: function() {
    return {
      showAllRecipes: false
    }
  },
  computed: {
    name: function() {
      return this.user.meta.fn + " " + this.user.meta.ln;
    }
  }
}
