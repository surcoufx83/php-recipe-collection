const RecipeEditor = {
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
             var cooking = 0
             var preparing = 0
             var rest = 0
             for (i=0; i<parent.page.currentRecipe.preparation.steps.length; i++) {
               if (parent.page.currentRecipe.preparation.steps[i].timeConsumed.cooking !== "")
                 cooking += parseInt(parent.page.currentRecipe.preparation.steps[i].timeConsumed.cooking)
               if (parent.page.currentRecipe.preparation.steps[i].timeConsumed.preparing !== "")
                 preparing += parseInt(parent.page.currentRecipe.preparation.steps[i].timeConsumed.preparing)
               if (parent.page.currentRecipe.preparation.steps[i].timeConsumed.rest !== "")
                 rest += parseInt(parent.page.currentRecipe.preparation.steps[i].timeConsumed.rest)
             }
             parent.page.currentRecipe.preparation.timeConsumed.cooking = cooking
             parent.page.currentRecipe.preparation.timeConsumed.preparing = preparing
             parent.page.currentRecipe.preparation.timeConsumed.rest = rest
             parent.page.currentRecipe.preparation.timeConsumed.total = cooking + preparing + rest
           } else {
             parent.progress.responseSuccess = false
           }
           parent.progress.response.code = data.code
           parent.progress.response.message = data.message
           parent.progress.sending = false
         })
       }
    },
    onIngredientDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.ingredients.splice(i, 1)
    },
    onIngredientAddBtnClick: function() {
      this.page.currentRecipe.preparation.ingredients.push({
        description: '',
        id: 0,
        quantity: '',
        quantityCalc: '',
        unit: {
          id: 0,
          name: ''
        },
        unitId: 0
      })
    },
    onStepDelBtnClick: function(i) {
      this.page.currentRecipe.preparation.steps.splice(i, 1)
    },
    onStepAddBtnClick: function() {
      this.page.currentRecipe.preparation.steps.push({ index: 0, name: '', userContent: '', timeConsumed: { cooking: '', preparing: '', rest: '', unit: 'minutes' } })
    },
    onGotoRecipeBtnClick: function(i) {
      console.log('@onGotoRecipeBtnClick')
      router.push({ name: 'recipe', params: { id: this.page.currentRecipe.id, name: this.page.currentRecipe.name } })
    }
  }
}
