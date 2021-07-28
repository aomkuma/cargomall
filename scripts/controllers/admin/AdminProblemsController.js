angular.module('app').controller('AdminProblemsController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
    $templateCache.removeAll();

    $scope.section = 'calc-transport-cost';
    

  $scope.getList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition};
      HTTPService.clientRequest('problems/list', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.DataList = result.data.DATA.DataList;

        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
  }


  $scope.dialogUpdateData = function(data){

      $scope.ProblemData = angular.copy(data);

      if($scope.ProblemData == null){
        $scope.setDefaultData();
      }

      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/dialog-problems-admin.html',
          size : 'md',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogReturnFromOKBtnCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.confitmUpdateData(valResult);
      });
  }

  $scope.confitmUpdateData = function(ProblemData){
      IndexOverlayFactory.overlayShow();
      var params = {'ProblemData' : ProblemData};
      HTTPService.clientRequest('problems/update/by-admin', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          window.location.reload();

        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
  }

  $scope.setDefaultData = function(){
    $scope.ProblemData = {'id' : null, 'title' : null, 'user_comment' : null, 'status' : 'notify'};
  }

  $scope.condition = {'keyword' : null, 'status' : null};
  $scope.ProblemData = null;
  $scope.setDefaultData();

  $scope.getList();

});
