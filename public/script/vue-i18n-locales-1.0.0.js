
messages = JSON.parse($('i18n').text())

const i18n = new VueI18n({
  locale: navigator.language,
  messages: messages
});
