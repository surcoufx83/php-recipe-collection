
const rc_i18n_de = {
  common: {
    yesno: {
      no: {
        uc: 'Nein',
        lc: 'nein'
      },
      yes: {
        uc: 'Ja',
        lc: 'ja'
      }
    }
  },
  greetings: {
    hello: 'Hallo {user}'
  },
  modals: {
    failed: {
      head: 'Fehler',
      subject: 'Bei der Aktion ist leider ein Fehler aufgetreten.',
      close: 'Zurück'
    }
  },
  navbar: {
    search: {
      placeholder: 'Suche nach Rezepten oder Zutaten, z.B. Kartoffelauflauf'
    }
  },
  pages: {
    common: {
      navtitle: 'Kochbuch'
    },
    editRecipe: {
      subtitle: '',
      title: 'Rezept überarbeiten'
    },
    home: {
      introtext1: 'Schön das du in das Kochbuch schaust. Wir arbeiten permanent dran, es besser zu machen. Wenn dir Fehler auffallen, melde diese gerne an Stefan oder Elias.',
      introtext2: 'Auch wenn einige Funktionen noch nicht so ganz hinhauen, Rezepte eintragen, bearbeiten, anschauen und nachkochen klappt schon ganz gut.',
      mobile1: 'Du kannst die Seitenleiste anzeigen, indem du auf das',
      mobile2: 'Menü-Symbol oben rechts klicks.',
      subtitle: '',
      title: '',
      workingon: 'An dieser Stelle werden demnächst neue Rezepte vorgestellt. Bis dahin nutze eine der Funktonen aus der Seitenleiste.'
    },
    login: {
      cloud: {
        description: 'Du hast schon mal unsere Nextcloud verwendet? Dann nutze dieses Konto um dich hier anzumelden. Einfach und sicher!',
        failed: 'Die Anmeldung über die Nextcloud ist fehlgeschlagen.',
        header: 'Anmeldung mit Nextcloud-Konto',
        submit: 'Mit Nextcloud-Konto anmelden'
      },
      failed: {
        loginFailed: 'Anmeldung fehlgeschlagen.'
      },
      keepSession: {
        description: 'Deine Anmeldung bleibt auch nach dem Schließen des Webbrowsers gültig.',
        title: 'Angemeldet bleiben.'
      },
      nameField: {
        invalidFeedback: 'Bitte gib deine E-Mailadresse ein.',
        placeholder: 'E-Mailadresse'
      },
      lostPassword: {
        title: 'Passwort verloren?'
      },
      passwordField: {
        invalidFeedback: 'Bitte gib dein Passwort ein.',
        placeholder: 'Passwort'
      },
      regular: {
        description: 'Sofern du einen dedizierten Benutzeraccount für diese Webseite hast, kannst du dich hier mit einer E-Mailadresse und deinem Passwort anmelden.',
        header: 'Anmeldung mit E-Mailadresse'
      },
      submitButton: {
        title: 'Anmelden'
      },
      title: 'Benutzer Anmeldung'
    },
    logout: {
      subtitle: '',
      title: '',
      header: 'Abmeldung erfolgt ...'
    },
    myRecipes: {
      subtitle: '',
      title: 'Meine Rezepte'
    },
    random: {
      subtitle: 'Kurzen Moment, ich suche gerade nach einem Rezept...',
      title: 'Zufälliges Rezept'
    },
    recipe: {
      subtitle: '',
      title: '{recipe}',
      titleWithUser: '{recipe} von {user}',
      actionbtn: {
        edit: 'Bearbeiten'
      },
      actions: {
        title: 'Rezept verwalten',
        edit: {
          title: 'Bearbeiten',
          tooltip: 'Im Bearbeiten-Modus kannst du alle Angaben überarbeiten.'
        },
        publish: {
          title: 'Veröffentlichen',
          tooltip: 'Durch das Veröffentlichen dieses Rezeptes wird es für andere NutzerInnen auffindbar.'
        },
        reject: {
          title: 'Zurückziehen',
          tooltip: 'Die Veröffentlichung wird zurückgezogen. Damit kann das Rezept nicht mehr gefunden werden.'
        }
      },
      administration: {
        title: 'Administration',
        author: 'Verfasst von {0}',
        deletedAuthorAccount: 'Verfasst von [Gelöschtes Konto]',
        removeRecipe: 'Rezept entfernen',
        unpublishRecipe: 'Nicht-Öffentlich setzen'
      },
      cookingSteps: {
        header: 'Zubereitung',
        placeholder: 'Schritt'
      },
      duration: {
        overallPrefix: 'Für dieses Rezept ist eine Zubereitungsdauer von insgesamt ',
        overallSuffix: ' angegeben.',
        description: 'Hinweis: Die Zubereitung des Gerichts nimmt viel Zeit in Anspruch.',
        preparation: 'Vorbereitungszeit: %s',
        preparationHeader: 'Vorbereitungszeit',
        cooking: 'Kochzeit: %s',
        cookingHeader: 'Kochzeit',
        rest: 'Ruhezeit: %s',
        restHeader: 'Ruhezeit',
        notSet: 'keine Angabe'
      },
      footer: {
        gallery: 'Bilder',
        ingredients: 'Zutaten',
        steps: 'Zubereitung'
      },
      gallery: {
        header: 'Bildergalerie'
      },
      header: {
        unpublishedAlert: {
          description: 'Das Rezept wurde bisher nicht veröffentlicht und ist damit für andere Benutzer nicht zu finden.',
          link: 'Im Bearbeiten-Modus kannst du das Rezept freigeben.'
        },
        about: {
          title: 'Über dieses Rezept',
          publication: {
            nouser: 'Das Rezept wurde am {date} veröffentlicht.',
            fromuser: 'Dieses Rezept wurde am {date} von {user} veröffentlicht.'
          },
          socialstats: {
            nothingyet: 'Es wurde bisher noch nicht nachgekocht und auch nicht bewertet.',
            cookedonly: 'Es wurde {count} mal nachgekocht, allerdings noch nicht bewertet.',
            votedonce: 'Es wurde bisher mit einem Herzen bewertet.|Es wurde bisher mit {value} Herzen bewertet.',
            votedmulti: 'Es wurde im Schnitt mit einem Herzen bewertet.|Es wurde im Schnitt mit {value} Herzen bewertet.',
            cookedAndVoted: 'Es wurde {count} mal nachgekocht und im Schnitt mit {value} Herzen bewertet.'
          },
          source: 'Herkunft:'
        }
      },
      ingredients: {
        header: 'Zutatenliste',
        description: 'Das Rezept ist ausgelegt für <strong>{0}</strong> Personen.',
        ingredient: 'Zutatenbeschreibung',
        inputPrefix: 'Mengenangaben umrechnen für: ',
        inputPersons: ' Personen',
        quantity: 'Mengenangabe'
      },
      options: {
        header: 'Optionen',
        publication: {
          publish: 'Veröffentlichen',
          unpublish: 'Veröffentlichung zurückziehen'
        },
        edit: 'Bearbeiten',
        toGallery: 'Bildergalerie',
        toRecipe: 'Zurück'
      },
      preparation: {
        timeconsumption: {
          title: 'Zubereitungsdauer',
          overall: 'Für dieses Rezept ist eine Zubereitungsdauer von insgesamt <strong>{duration}</strong> angegeben.',
          ltwarn: 'Hinweis: Die Zubereitung des Gerichts nimmt viel Zeit in Anspruch.',
          preparing: 'Vorbereitungszeit:',
          preparingShort: 'Vorbereiten',
          resting: 'Ruhezeit:',
          restingShort: 'Ruhen',
          cooking: 'Koch-/Backzeit:',
          cookingShort: 'Kochen',
          notset: 'keine Angabe'
        },
        steps: {
          title: 'Zubereitungsschritte',
          placeholder: 'Schritt'
        }
      },
      rate: {
        button: 'Bewertung abgeben',
        header: 'Rezept bewerten',
        description: 'Du hast das Rezept nachgekocht? Das Essen war super lecker oder doch nicht so gelungen? Lass es uns und wissen und bewerte das Rezept.',
        voted: {
          subject: 'Du hast bereits eine Bewertung abgegeben. Du kannst das Rezept erst in einigen Tagen erneut bewerten.'
        },
        modal: {
          title: 'Rezeptbewertung',
          description: 'Mit der Beantwortung der folgenden kurzen Fragen gibst du eine direkte Rückmeldung an den Autoren des Rezepts. Du kannst jederzeit abbrechen. Erst durch einen Klick auf <i>Bewertung speichern</i> wird deine Bewertung übernommen.',
          cooked: {
            title: 'Hast du das Rezept nachgekocht?',
            none: 'Keine Angabe'
          },
          rating: {
            title: 'Wie schwierig war es?',
            description: 'Wie schwer fandest du es, dass Rezept nachzukochen? War alles beschrieben was du benötigst oder haben sich dir viele Fragezeichen aufgetan?',
            none: 'Keine Angabe',
            easy: 'Leicht',
            medium: 'Geht so',
            hard: 'Kompliziert'
          },
          voting: {
            title: 'Wieviele Herzen hat das Gericht verdient?',
            description: 'Bewerte mit 1 (schlecht) bis 5 (sehr gut), wie gut dir das Gericht geschmeckt hat.'
          },
          submitting: 'Die Bewertung wird gerade an den Server geschickt. Das sollte nicht länger als eine Sekunde dauern ...',
          submitted: 'Deine Bewertung wurde gespeichert. Vielen Dank!',
          error: 'Es gab einen Fehler bei der Datenübertragung &#x1F613;',
          save: 'Bewertung speichern',
          close: 'Zurück'
        }
      },
      stats: {
        header: 'Statistiken',
        cooked: {
          times: '{0} &times; gekocht'
        },
        views: {
          times: '{0} &times; gesehen'
        }
      }
    },
    recipes: {
      subtitle: '',
      title: 'Rezeptsammlung',
      filtered: {
        own: {
          title: 'Meine Rezepte'
        },
        user: {
          title: 'Rezepte von {user}'
        }
      },
      unfiltered: {
        title: 'Rezepte erkunden'
      },
      common: {
        goto: 'Zum Rezept',
        submittedBy: 'Eingetragen von {0}',
        votings: 'Keine Bewertung|{count} Bewertung|{count} Bewertungen'
      }
    },
    search: {
      subtitle: '',
      title: '',
      searchingFor: 'Suchen nach',
      inputPlaceholder: 'z. B. Pizza Olive',
      inputButtonText: 'Finden',
      results: {
        gotoRecipe: 'Zum Rezept',
        header: 'Wir haben keine Ergebnisse gefunden.|Wir haben ein Ergebnis gefunden!|Wir haben {num} Rezepte gefunden!'
      }
    },
    userRecipes: {
      subtitle: '',
      title: 'Rezepte von {name}'
    },
    writeRecipe: {
      subtitle: '',
      title: 'Neues Rezept erfassen',
      intro: {
        header: 'Rezepteingabe',
        requiredFields: 'Alle mit <b><span class="text-contrast">*</span></b> markierten Felder müssen befüllt werden. Der Großteil der Angaben ist optional, aber wir freuen uns, wenn du diese trotzdem befüllst.',
        subject: 'Vielen Dank, dass du ein neues Rezept zu unserer Sammlung beitragen möchtest!<br />Das von dir bereitgestellte Rezept ist anfangs als <i>Privat</i> markiert. Du bekommst nach dem Speichern die Möglichkeit, dass Rezept zu prüfen und zu veröffentlichen.',
        editHeader: 'Rezept bearbeiten'
      },
      chapter1: {
        header: 'Allgemeine Angaben',
        recipeName: {
          title: 'Name für dieses Gericht',
          description: 'z.B. Vogtländischer Sauerbraten mit Rotkohl und Klößen; Pflichtfeld',
          invalidFeedback: 'Bitte das Feld befüllen. Die Angabe eines Names ist zwingend erforderlich.',
          placeholder: 'Name für dieses Gericht',
        },
        recipeDescription: {
          title: 'Zusatzinformationen und Detailbeschreibung',
          description: 'Weitergehende Informationen, Detailbeschreibung, Originalname; Optional',
          placeholder: 'Beschreibung',
        },
        recipeEater: {
          title: 'Für wie viele Personen?',
          description: 'Gib eine Zahl an, für wie viele die Personen die Menge an Zutaten ausreicht.; Pflichtfeld',
          invalidFeedback: 'Die Anzahl der Personen ist zwingend erforderlich (Min = 1, Max = 100).',
          placeholder: '',
        }
      },
      chapter2: {
        addButton: {
          title: 'Anklicken um weitere Zutaten hinzuzufügen.'
        },
        delButton: {
          title: 'Anklicken um diese Zeile zu entfernen.'
        },
        quantity: {
          header: 'Menge',
          placeholder: 'z.B. 6'
        },
        unit: {
          header: 'Einheit',
          noUnit: 'Keine Einheit',
          placeholder: 'z.B. EL'
        },
        ingredient: {
          header: 'Zutat',
          placeholder: 'z.B. Olivenöl'
        },
        header: 'Zutatenliste',
        description: 'Gib nachfolgend alle Zutaten an, welche für das Rezept benötigt werden. Die Mengenangabe kannst du auch weglassen (z.B. für etwas Salz zum Würzen).'
      },
      chapter3: {
        header: 'Bildergallerie',
        pictures: {
          title: 'Bilder hochladen',
          description: 'Lade ein oder mehrere Bilder von deinem Computer bzw. Handy hoch um diese neben dem Rezept anzuzeigen. Das erste Bild wird immer das Vorschaubild sein.',
          remove: 'Bild löschen',
          upload: 'Hinzufügen',
        },
        header2: 'Zur Bildergallerie',
        togalleryLink: 'Die Bilder deiner Rezepte kannst du in der Galerie verwalten.'
      },
      chapter4: {
        addButton: {
          title: 'Anklicken um einen weiteren Arbeitsschritt einzufügen.'
        },
        description: 'Nutze diesen Abschnitt um die Schritte der Zubereitung zu beschreiben. Du kannst alles in einen Block packen oder mehrere Schritte über das Plus-Zeichen anlegen. Optional kannst du für jeden Schritt auch noch angeben, wie lange Vorbereitung, Ruhezeit, und das Kochen dauern.',
        header: 'Schritt-für-Schritt Anleitung',
        step: {
          stepname: 'Schritt',
          duration: {
            cooking: 'Kochdauer',
            description: 'Zeitaufwand (Minuten)',
            preparation: 'Vorbereitung',
            rest: 'Ruhezeit'
          },
          preparation: {
            description: 'Anweisungen',
            invalidFeedback: 'Bitte gib einige Informationen über die Zubereitung an.',
            placeholder: 'Bitte fülle hier eine detaillierte Beschreibung der Vorbereitung aus.',
          },
          title: {
            description: 'Überschrift für diesen Abschnitt',
            placeholder: 'Trage hier eine kurze Überschrift ein.'
          }
        }
      },
      chapter5: {
        header: 'Weitere Optionen',
        sourceText: {
          title: 'Quelle',
          description: 'Gib gerne an, woher dieses Rezept stammt (z.B. Oma Christines Kochbuch); Optional',
          placeholder: 'Quellenbeschreibung',
        },
        sourceUrl: {
          title: 'Quelle: Internet',
          description: 'Du hast das Rezept aus dem Internet übernommen? Bitte gib hier die Originaladresse an.; Optional',
          placeholder: 'www.rezepte.de/rezept-1',
        },
        tags: {
          title: 'Tags angeben',
          description: 'Tags markieren ein Rezept mit kurzen Schlagwörtern. Jedem Rezept können beliebig viele dieser Tags zugeordnet werden (z.B. \'Dinner\' und \'Für Zwei\').; Optional',
          placeholder: 'Kein Tag gewählt.'
        }
      },
      actions: {
        submitButton: {
          title: 'Rezept speichern',
          editTitle: 'Änderungen speichern',
          submitted: 'Rezept wurde gespeichert'
        }
      },
      modal: {
        backButton: 'Zurück',
        preparing: 'Deine Eingaben werden geprüft.',
        description: 'Die Rezeptdaten werden gerade ins Kochbuch geschrieben. Bitte habe kurz Geduld, es geht gleich weiter...',
        error: 'Das hätte nicht passieren sollen, aber beim Speichern ist ein Fehler aufgetreten. Die Fehlerinformationen wurden an Stefan gesendet.',
        forwardButton: 'Zum Rezept',
        newButton: 'Noch ein Rezept eintragen',
        success: 'Das Rezept ist gespeichert. Wähle einen der Buttons um fortzufahren.',
        title: 'Rezept wird gespeichert...'
      },
      validation: {
        missingInfo: 'Es gibt noch fehlerhafte Felder. Bitte prüfe deine Angaben.',
        missingIngredient: 'Mindestens eine Zutat angeben.',
        missingStep: 'Mindestens einen Zubereitungsschritt angeben.'
      }
    }
  },
  responseMessages: {
    badArgumentsException: 'Ungültige Argumente angegeben.',
    badRequestException: 'Ungültige Anfrage.',
    dbInsertException: 'Fehler beim Einfügen von Daten in die Datenbank.',
    dbSelectException: 'Beim Abrufen von Datensätzen aus der Datenbank ist ein Fehler aufgetreten.',
    dbStmtException: 'Fehler bei der Vorbereitung der Datenbank-Anweisung.',
    dbUpdateException: 'Fehler beim Speichern der Änderungen in der Datenbank.',
    functionNotFoundException: 'Die angeforderte Funktion ist leider noch nicht programmiert. Stefan hauen, oder etwas Geduld zeigen ;)',
    insufficientPermissionException: 'Unzureichende Berechtigung für diese Aktion.',
    loginFailed: 'Anmeldung fehlgeschlagen.',
    loginSuccessfull: 'Anmeldung erfolgreich.',
    maintenanceException: 'Wartungsmodus ist aktiviert.',
    noApiKeyException: 'Der Zugriff erfordert ein Anmelde-Token. Bitte melden Sie sich zuerst über die Website an, bevor Sie den API-Zugang versuchen.',
    noChanges: 'Es wurden keine Änderungen erkannt.',
    noResults: 'Die Suche hat keine Ergebnisse zurückgeliefert.',
    notAllowedException: 'Keine Berechtigung für diese Aktion.',
    notAuthenticatedException: 'Benutzer nicht eingeloggt.',
    pageMovedException: 'Die angeforderte Seite wurde verschoben und kann nicht mehr per Webanforderung oder CLI abgerufen werden.',
    parameterException: 'Für die angegebenen Parameter ist keine Verarbeitung konfiguriert.',
    sendMailFailed: 'Die Mail konnte nicht gesendet werden: ',
    undefinedException: 'Im Backend ist ein nicht spezifizierter Fehler aufgetreten. Ihre Änderungen wurden nicht akzeptiert.',
    validationSucceeded: 'Die Überprüfung war erfolgreich. Es wurden keine Fehler festgestellt.',
    validationFailed: 'Es sind folgende Fehler aufgetreten: '
  },
  sidebar: {
    admin: {
      configuration: 'Konfiguration',
      cronjobs: 'Cronjobs',
      logs: 'Server-Log',
      title: 'Administration',
      translations: 'Übersetzung',
      users: 'Benutzer'
    },
    close: 'Ausblenden',
    home: 'Startseite',
    myRecipes: 'Meine Rezepte',
    random: 'Zufallsrezept',
    search: 'Suche',
    writeRecipe: 'Neues Rezept'
  }
}
