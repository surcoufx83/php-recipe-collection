
console.log($('i18n').text())
messages = JSON.parse($('i18n').text())
console.log(messages)

const i18n = new VueI18n({
  locale: navigator.language,
  messages: messages
});
