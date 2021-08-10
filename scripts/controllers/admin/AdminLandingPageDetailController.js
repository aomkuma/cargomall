angular.module('app').controller('AdminLandingPageDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, $routeParams, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    // $log.log($scope.session_storage.user_data);
        $scope.clearTimeout();
    if(!checkEmptyField($scope.session_storage.user_data)){
      // alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      window.location.replace('admin/signin');
      // history.back();
      return;
    }

    if(!checkEmptyField($scope.UserData.is_admin) && !$scope.UserData.is_admin){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      history.back();
      return;
    }

    $templateCache.removeAll();

    var ckEditorConfig = {
            extraPlugins: 'uploadimage,image2,filebrowser,colorbutton',
            height: 300,
            uploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
            filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
            filebrowserImageBrowseUrl: 'ckfinder/ckfinder.html?type=Images',
            filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            filebrowserImageUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            contentsCss: [ CKEDITOR.basePath + 'contents.css', 'https://sdk.ckeditor.com/samples/assets/css/widgetstyles.css' ],
            image2_alignClasses: [ 'image-align-left', 'image-align-center', 'image-align-right' ],
            image2_disableResizer: true,
            height:'400px',
            toolbar : [
                { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                '/',
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                { name: 'Page', items: [ 'Page' ] }
            ]

        };

    $scope.getData = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'id' : $scope.id };
      HTTPService.clientRequest('admin/landing-page/get', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        $scope.Data =  result.data.DATA;
        $scope.Data.start_date = makeDate($scope.Data.start_date);
        $scope.Data.end_date = makeDate($scope.Data.end_date);

        if (CKEDITOR.instances.editor1) CKEDITOR.instances.editor1.destroy();

        CKEDITOR.config.extraPlugins = 'colorbutton';
        CKEDITOR.config.extraPlugins = 'emoji';
        CKEDITOR.config.colorButton_enableAutomatic = false;

        CKEDITOR.replace( 'editor1',ckEditorConfig );

        $scope.preview = $scope.Data.text_desc;

      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.updateData = function(Data){

      var UpdateData = angular.copy(Data);
      
      UpdateData.text_desc = CKEDITOR.instances.editor1.getData();
      UpdateData.start_date = makeSQLDate(UpdateData.start_date);
      UpdateData.end_date = makeSQLDate(UpdateData.end_date);

      IndexOverlayFactory.overlayShow();
      var params = {'Data' : UpdateData, 'AttachFile' : $scope.AttachFile};
      HTTPService.uploadRequest('admin/landing-page/update', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        window.location.href = 'admin/landing-page';
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.previewTemplate = function(){
      $scope.Data.text_desc = CKEDITOR.instances.editor1.getData();
      $scope.$parent.LandingPage = angular.copy($scope.Data);
    }

    $scope.id = null;
    if(checkEmptyField( $routeParams.id )){
      $scope.id = $routeParams.id;
      $scope.getData();
    }else{
      if (CKEDITOR.instances.editor1) CKEDITOR.instances.editor1.destroy();

      CKEDITOR.config.extraPlugins = 'colorbutton';
      CKEDITOR.config.extraPlugins = 'emoji';
      CKEDITOR.config.colorButton_enableAutomatic = false;

      CKEDITOR.replace( 'editor1',ckEditorConfig );
    }

});
