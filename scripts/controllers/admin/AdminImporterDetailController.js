angular.module('app').controller('AdminImporterDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $routeParams, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
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

    $scope.getUserList = function(){
      HTTPService.clientRequest('admin/user/list', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.UserList =  result.data.DATA.DataList;
        }
      });
    }

    $scope.getUserAddress = function(user_id){

        IndexOverlayFactory.overlayShow();
        var params = {'user_id' : user_id};
        HTTPService.clientRequest('admin/user/address', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.UserAddress = result.data.DATA.addresses;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
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

    $scope.loadData = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'importer_id' : $scope.importer_id};
        HTTPService.clientRequest('importer/get', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.Importer = result.data.DATA;
                $scope.Customer = result.data.DATA.customer;

                if(checkEmptyField($scope.Importer.china_arrival)){
                  $scope.Importer.china_arrival = makeDateTime($scope.Importer.china_arrival);
                  console.log($scope.Importer.china_arrival);
                }
                if(checkEmptyField($scope.Importer.china_departure)){
                  $scope.Importer.china_departure = makeDateTime($scope.Importer.china_departure);
                }
                if(checkEmptyField($scope.Importer.thai_arrival)){
                  $scope.Importer.thai_arrival = makeDateTime($scope.Importer.thai_arrival);
                }
                if(checkEmptyField($scope.Importer.thai_departure)){
                  $scope.Importer.thai_departure = makeDateTime($scope.Importer.thai_departure);
                }
                $scope.loadTransportRateData();
                
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.updateImporterStatus = function(importer_id, importer_status, type){

        var to_importer_status = importer_status;
        if(type == 'back'){
            to_importer_status = parseInt(importer_status) - 1;
        }else if(type == 'next'){
            to_importer_status = parseInt(importer_status) + 1;
        }

        IndexOverlayFactory.overlayShow();
        var params = {'importer_id' : importer_id, 'to_importer_status' : to_importer_status};
        HTTPService.clientRequest('admin/importer/status/update', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                window.location.reload();
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.updateImporter = function(Importer){

        IndexOverlayFactory.overlayShow();
        var params = {'Data' : Importer};
        HTTPService.clientRequest('importer/update', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                // window.location.href = 'admin/order';
                // window.location.reload();
                if($scope.importer_id == undefined){
                  window.location.href = 'admin/importer';
                }
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.calcPrice = function(){
      var importer = angular.copy($scope.Importer);
      var transport_rate_kg = null;
      var transport_rate_cbm = null;
      if(importer.transport_type == 'car'){
        // calc by kg 

        // find by prod desc
        var index = $scope.findProductRate($scope.TransportRateData.rate_car_kg , importer.product_type);
        // alert(index);
        transport_rate_kg = $scope.TransportRateData.rate_car_kg[index];

        index = $scope.findProductRate($scope.TransportRateData.rate_car_cbm , importer.product_type);
        transport_rate_cbm = $scope.TransportRateData.rate_car_cbm[index];
        // $scope.TransportRateData.rate_sea_kg 
        // calc by cbm

      }else if(importer.transport_type == 'sea'){
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

    $scope.setTotalPriceYuan = function(type){
      if(type == 'KG'){
        $scope.Importer.total_price_yuan = $scope.rateByKG;
      }else{
        $scope.Importer.total_price_yuan = $scope.rateByCBM;
      }
    }

    $scope.importer_id = $routeParams.importer_id;
    $scope.rateByKG = 0;
    $scope.rateByCBM = 0;
    console.log($scope.importer_id);
    if($scope.importer_id){
      $scope.loadData();
    }else{
      $scope.getUserList();
    }
    
    
});
