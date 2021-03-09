angular.module('app').controller('MemberDashboardController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.loadTrackingNoneOwnerList = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'condition' : {'limit' : 10}};
        HTTPService.clientRequest('tracking-none-owner/list', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.TrackingNoneOwnerList = result.data.DATA.DataList;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.getOrderList = function(){

      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition};
      HTTPService.clientRequest('order/list/by-user/limit', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.OrderList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getImporterList = function(){

      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition};
      HTTPService.clientRequest('importer/list/by-user/limit', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.ImporterList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getCargoAddressList = function(){

      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition};
      HTTPService.clientRequest('cargo-address/list', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.CargoAddressList = result.data.DATA.DataList;
          
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

    $scope.calcCbm = function(){
      if($scope.Importer.width && $scope.Importer.longs && $scope.Importer.height){
        $scope.Importer.cbm = (parseFloat($scope.Importer.width) * parseFloat($scope.Importer.longs) * parseFloat($scope.Importer.height)) / 1000000;
        // alert($scope.Importer.cbm);
        $scope.calcPrice();
      }
    }

    $scope.calcPrice = function(){
      var importer = angular.copy($scope.Importer);
      var transport_rate_kg = null;
      var transport_rate_cbm = null;
      if(importer.transport_type == 'sea'){
        // calc by kg 

        // find by prod desc
        var index = $scope.findProductRate($scope.TransportRateData.rate_car_kg , importer.product_type);
        // alert(index);
        transport_rate_kg = $scope.TransportRateData.rate_car_kg[index];

        index = $scope.findProductRate($scope.TransportRateData.rate_car_cbm , importer.product_type);
        transport_rate_cbm = $scope.TransportRateData.rate_car_cbm[index];
        // $scope.TransportRateData.rate_sea_kg 
        // calc by cbm

      }else if(importer.transport_type == 'car'){
        var index = $scope.findProductRate($scope.TransportRateData.rate_sea_kg , importer.product_type);
        transport_rate_kg = $scope.TransportRateData.rate_sea_kg[index];

        index = $scope.findProductRate($scope.TransportRateData.rate_sea_cbm , importer.product_type);
        transport_rate_cbm = $scope.TransportRateData.rate_sea_cbm[index];
      }

      // calc kg
      var weight_kgm = 0;
      var cbm = 0;
      if(checkEmptyField(importer.weight_kgm)){
        weight_kgm = parseFloat(importer.weight_kgm);
      }
      if(checkEmptyField(importer.cbm)){
        cbm = parseFloat(importer.cbm);
      }

      if(cbm < 2){
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_1;
      }else if(cbm >= 2 && cbm < 5){
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_2;
      }else{
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_3;
      }

      if(weight_kgm < 100){
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_1;
      }else if(weight_kgm >= 100 && cbm < 500){
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_2;
      }else{
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_3;
      }
      $log.log($scope.rateByCBM , $scope.rateByKG);
    }

    $scope.findProductRate = function(transport_rate, product_type){
      $log.log(transport_rate);
      for(var i = 0; i < transport_rate.length; i++){
        if(transport_rate[i].product_desc == product_type){
          // $log.log(transport_rate[i].product_desc , product_type);
          return i;
        }
      }
    }

    $scope.loadTransportRateData = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'rate_level' : $scope.Customer.user_level};
        HTTPService.clientRequest('admin/transport-rate/get', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.TransportRateData = result.data.DATA;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            $scope.calcPrice();
            IndexOverlayFactory.overlayHide();
        });
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
    $scope.getOrderList();
    $scope.loadTrackingNoneOwnerList();
    $scope.getImporterList();
    $scope.getCargoAddressList();
    $scope.LandingPage = sessionStorage.getItem('landing_page');
    
    if($scope.LandingPage == null){
      $scope.loadLandingPage();
        //$scope.$parent.currentUser = angular.fromJson($user_session);
    }else{
       //window.location.replace('#/guest/logon');
    }
    

});
