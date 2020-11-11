
messages = {
  de: rc_i18n_de,
  'de-AT': rc_i18n_de,
  'de-CH': rc_i18n_de,
  'de-DE': rc_i18n_de,
  en: rc_i18n_de,
  'en-GB': rc_i18n_de,
  'en-US': rc_i18n_de
}

const i18n = new VueI18n({
  locale: navigator.language,
  messages: messages
});
