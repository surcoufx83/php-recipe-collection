const RecipesCreator = {
  delimiters: ['${', '}'],
  props: ['page', 'user'],
  template: '#recipe-write-template',
  data: function() {
    return {
      form: {
        completed: false,
        validationFailed: false,
      },
      progress: {
        preparing: false,
        sending: false,
        gotresponse: false,
        responseSuccess: false,
        responseId: 0,
        response: {
          code: 1,
          message: ''
        }
      }
    }
  },
  methods: {
    onSubmit: function(e) {
      e.preventDefault()
      if (this.form.completed) {
        $('#new-recipe-modal').modal('show')
        return
      }
      var missinginfo = false
      if ( this.page.currentRecipe.preparation.ingredients.length == 0
        || this.page.currentRecipe.preparation.steps.length == 0)
        missinginfo = true
      $('.needs-validation').find('input,select,textarea').each(function () {
        // check element validity and change class
        $(this).removeClass('is-valid is-invalid')
        var result = this.checkValidity()
        $(this).addClass(result ? 'is-valid' : 'is-invalid')
        if (!result)
          missinginfo = true
       });
       this.form.validationFailed = missinginfo
       if (!missinginfo) {
         this.progress.preparing = true
         $('#new-recipe-modal').modal('show')
         var formdata = new FormData($('#new-recipe-form')[0])
         this.progress.preparing = false
         this.progress.sending = true
         const parent = this
         postFormData(app.$route.path, formdata, function(data) {
           parent.progress.gotresponse = true
           if (data.success) {
             parent.form.completed = true
             parent.progress.responseSuccess = true
             parent.progress.responseId = data.recipeId
           } else {
             parent.progress.responseSuccess = false
           }
           parent.progress.response.code = data.code
           parent.progress.response.message = data.message
           parent.progress.sending = false
         })
       }
    },
    onPictureAddBtnClick: function() {
      var i = this.page.currentRecipe.pictures.length
      this.page.currentRecipe.pictures.push({ file: null })
      const parent = this
      setTimeout(function() {
        // wait for upload button to be created
        var i = parent.page.currentRecipe.pictures.length - 1
        $('#file-' + i).click()
      }, 100)
    },
    onPictureDelBtnClick: function(i) {
      this.page.currentRecipe.pictures.splice(i, 1)
    },
    onPictureInput: function(i) {
      if (!window.FileReader)
        return
      if (!this.page.currentRecipe.pictures[i].file) {
        $('#picture-image-' + i).css("content", "none")
        return
      } else {
        if (/^image/.test(this.page.currentRecipe.pictures[i].file.type)) {
          var reader = new FileReader()
          reader.readAsDataURL(this.page.currentRecipe.pictures[i].file)
          reader.onloadend = function() {
            $('#picture-image-' + i).css("content", "url(" + this.result + ")")
          }
        }
      }
    },
    onIngredientDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.ingredients.splice(i, 1)
    },
    onIngredientAddBtnClick: function() {
      this.page.currentRecipe.preparation.ingredients.push({ amount: '', unit: '', description: '' })
    },
    onStepDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.steps.splice(i, 1)
    },
    onStepAddBtnClick: function() {
      this.page.currentRecipe.preparation.steps.push({ index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } })
    },
    onNewRecipeBtnClick: function(i) {
      initEmptyRecipe(app)
      this.form.completed = false
      this.form.validationFailed = false
      this.progress.preparing = false
      this.progress.sending = false
      this.progress.gotresponse = false
      this.progress.responseSuccess = false
      this.progress.responseId = 0
      this.progress.response.code = 1
      this.progress.response.message = ''
      $('.needs-validation').find('input,select,textarea').each(function () {
        $(this).removeClass('is-valid is-invalid')
       });
      $('#new-recipe-modal').modal('hide')
    },
    onGotoRecipeBtnClick: function(i) {
      console.log('@onGotoRecipeBtnClick')
      router.push({ name: 'recipe', params: { id: this.progress.responseId, name: this.page.currentRecipe.name } })
    }
  }
}
