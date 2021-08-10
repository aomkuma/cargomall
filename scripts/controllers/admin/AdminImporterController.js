angular.module('app').controller('AdminImporterController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getImporterList = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
  		HTTPService.clientRequest('admin/importer/list', params).then(function(result){
  		if(result.data.STATUS == 'OK'){
  			
        $scope.DataList =  result.data.DATA.DataList;
        $scope.Pagination.totalPages = result.data.DATA.Total;
                
        // for(var i = 0; i < $scope.DataList.length; i++){
        //   if($scope.DataList[i].order_desc.china_ex_rate == 0){
        //     $scope.DataList[i].order_desc.china_ex_rate = $scope.exchange_rate;
        //   }
        // }
  		}else{
  		  var alertMsg = result.data.DATA;
  		  alert(alertMsg);
  		}
  		IndexOverlayFactory.overlayHide();
  		});
    }

    $scope.goToPage = function(page){
        $scope.getImporterList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.uploadExcel = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'AttachFile' : $scope.AttachFile};
      HTTPService.uploadRequest('admin/importer/upload', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        window.location.reload();
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.delete = function(id){

      $scope.alertMessage = 'ต้องการลบรายการนำเข้าสินค้านี้ ใช่หรือไม่ ?';

      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/dialog_confirm.html',
          size : 'sm',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.confirmDelete(id);
      });
    }

    $scope.confirmDelete = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'id' : id};
        HTTPService.clientRequest('admin/importer/delete', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.condition = {'keyword' : ''};

    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getImporterList();

});
