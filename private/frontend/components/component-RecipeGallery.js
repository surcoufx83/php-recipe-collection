const RecipeGallery = {
  delimiters: ['${', '}'],
  props: ['recipe', 'page', 'user'],
  template: '#recipe-gallery-template',
  computed: { },
  methods: {
    onAddPicture: function() {
      // console.log('Picture.onAddPicture')
      var i = this.page.currentRecipe.pictures.length
      this.page.currentRecipe.pictures.push({
        description: '',
        id: 0,
        index: i,
        link: '/pictures/_dummy.jpg',
        link350: '/pictures/_dummy.jpg',
        name: '',
        uploaded: '',
        uploadFile: null,
        uploaderId: this.user.id,
        uploaderName: this.user.meta.un
      })
      const parent = this
      setTimeout(function() {
        // wait for upload button to be created
        var i = parent.page.currentRecipe.pictures.length - 1
        $('#file-upload-' + i).click()
      }, 100)
    },
    onPictureAdded: function(i) {
      // console.log('Picture.onPictureAdded', i)
      if (!window.FileReader)
        return
      if (!this.page.currentRecipe.pictures[i].uploadFile) {
        $('#recipe-picture-' + i).css("content", "none")
        return
      } else {
        if (/^image/.test(this.page.currentRecipe.pictures[i].uploadFile.type)) {
          var reader = new FileReader()
          reader.readAsDataURL(this.page.currentRecipe.pictures[i].uploadFile)
          reader.onloadend = function() {
            $('#recipe-picture-' + i).css("content", "url(" + this.result + ")")
          }
          var data = new FormData()
          data.append('pictureUpload', this.page.currentRecipe.pictures[i].uploadFile)
          postFormData(app.$route.path, data, function(response) {
            if (response.success) {
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'description', response.picture.description)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'id', response.picture.id)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'index', response.picture.index)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'link', response.picture.link)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'link350', response.picture.link350)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'name', response.picture.name)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'uploaded', response.picture.uploaded)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'uploadFile', null)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'uploaderId', response.picture.uploaderId)
              app.$set(app.page.currentRecipe.pictures[response.picture.index], 'uploaderName', response.picture.uploaderName)
              $('#recipe-picture-' + response.picture.index).css("content", "none")
            } else {
              app.$set(app.page.modals.failedModal, 'message', app.$t(response.i18nmessage))
              app.$set(app.page.modals.failedModal, 'code', response.code)
              $('#action-failed-modal').modal('show')
            }
          })
        }
      }
    },
    onPictureDelBtnClick: function(i, id) {
      if (!this.page.currentRecipe.pictures[i])
        return
      $('#picture-delete-' + i).prop('disabled', true)
      const comp = this;
      postPageData(app.$route.path, {
        deleted: {
          index: i,
          id: id
        }
      },
      function(data) {
        if (!data.success) {
          app.$set(app.page.modals.failedModal, 'message', app.$t(data.i18nmessage))
          app.$set(app.page.modals.failedModal, 'code', data.code)
          $('#action-failed-modal').modal('show')
        } else {
          comp.page.currentRecipe.pictures.splice(i, 1)
        }
        $('#picture-delete-' + i).prop('disabled', false)
      })
    },
    onPictureMoved: function(evt) {
      // console.log('Picture.onPictureMoved', evt)
      postPageData(app.$route.path, {
        moved: {
          from: evt.oldIndex,
          to: evt.newIndex
        }
      },
      function(data) {
        if (!data.success) {
          app.$set(app.page.modals.failedModal, 'message', app.$t(data.i18nmessage))
          app.$set(app.page.modals.failedModal, 'code', data.code)
          $('#action-failed-modal').modal('show')
        }
      })
    },
    upldate: function(date) {
      return moment(date, moment.ISO_8601).format(app.user.customSettings.formats.date.short)
    }
  }
}
