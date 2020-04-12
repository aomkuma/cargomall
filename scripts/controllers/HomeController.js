angular.module('app').controller('HomeController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
    $templateCache.removeAll();

    /*$scope.session_storage = $localStorage;

    if(checkEmptyField($scope.session_storage.user_data)){
      $scope.UserData = angular.fromJson(atob($scope.session_storage.user_data));
      $log.log($scope.UserData.firstname);  
    }

    if(checkEmptyField($scope.session_storage.product_list_storage)){
      $scope.ProductListStorage = angular.fromJson($scope.session_storage.product_list_storage);
      $scope.TotalProductPiece = 0;
      for(var i = 0; i < $scope.ProductListStorage.length; i++){
        $scope.TotalProductPiece = parseInt($scope.ProductListStorage[i].product_qty);
      }
    }*/

    $scope.focusLinkUrl = function(){
        $scope.focusLinkURLInput = true;
    }
    
    $scope.loadTransportRate = function(){

        IndexOverlayFactory.overlayShow();
        var params = null;
        HTTPService.clientRequest('transport-rate/show', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.TransportRateData = result.data.DATA;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.setVideoUrl = function(url){
      $scope.videoURL = url;
    }

    
    $scope.videoURLList = [
                        {'id' : 'importer', 'display' : 'บริการนำเข้าสินค้า', 'value' : 'https://www.youtube.com/watch?v=BedioX543lg'},
                        {'id' : 'deposit', 'display' : 'บริการฝากชำระเงิน', 'value' : 'https://www.youtube.com/watch?v=k35NzrcZjPE'},
                        {'id' : 'transfer', 'display' : 'บริการโอนเงินไปจีน', 'value' : 'https://www.youtube.com/watch?v=uRQTMkHFpcM'},
                        {'id' : 'topup', 'display' : 'บริการเติมเงิน', 'value' : 'https://www.youtube.com/watch?v=3IUWQva33Dw'},
                        {'id' : 'order', 'display' : 'บริการฝากสั่งสินค้า', 'value' : 'https://www.youtube.com/watch?v=L1diC0Tk5v0'}
                        ];
    $scope.videoURL = $scope.videoURLList[0].value;

    $scope.LandingPage = null;
    $scope.focusLinkURLInput = false;

    $scope.loadTransportRate();

    $scope.LandingPage = sessionStorage.getItem('landing_page');
    
    if($scope.LandingPage == null){
      $scope.loadLandingPage();
        //$scope.$parent.currentUser = angular.fromJson($user_session);
    }else{
       //window.location.replace('#/guest/logon');
    }
    

});
