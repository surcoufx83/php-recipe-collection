Vue.component('rc-button', {
  delimiters: ['${', '}'],
  props: {
    badge: {
      /* Content for a badge inside the button */
      type: String,
      required: false
    },
    badgeicon: {
      /* Icon for a badge inside the button, must contain icon and space prop */
      type: Object,
      required: false
    },
    icon: {
      /* fa icon name, if set icon will be displayed in front of button text */
      type: String,
      required: false
    },
    iconClass: {
      /* custom css classes for the icon */
      type: String,
      required: false,
      default: ''
    },
    outline: {
      /* if set, outline variant will be used */
      type: Boolean,
      default: false,
      required: false
    },
    sm: {
      /* default: true, if set, smaller variant with less borders will be used */
      type: Boolean,
      default: true,
      required: false
    },
    space: {
      /* fa icon namespace (fas, far, etc) */
      type: String,
      required: false
    },
    subject: {
      /* event name that is emitted for the click event, if not set, title will be used */
      type: String,
      required: false
    },
    title: {
      /* text to show in the button, empty for icon only button */
      type: String,
      required: false,
      default: ''
    },
    variant: {
      /* css class of the button (default = common) */
      type: String,
      required: false,
      default: 'common'
    }
  },
  template: '#rc-button-template',
    methods: {
      onClick: function() {
        this.$emit('click', this.subject ? this.subject : this.title)
      },
    }
})
