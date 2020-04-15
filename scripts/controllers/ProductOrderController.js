angular.module('app').controller('ProductOrderController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';

    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    $scope.dialogRemoveItem = function(index){
        
        if(!$scope.$parent.ShowDialogRemoveItem){
            $scope.$parent.ShowDialogRemoveItem = true;
            $scope.$parent.RemoveItemIndex = index;
        }
    }

    $scope.autoUpdateOrder = function(index, product){
        $scope.ProductListStorage[index] = product;
        $localStorage.product_list_storage = JSON.stringify($scope.ProductListStorage);
    }

    // $scope.calcSum = function (){
    //     $scope.sumBaht = 0;
    //     angular.forEach($scope.ProductListStorage, function(value, key) {
    //         $log.log(value.product_size_choose);
    //         if(parseFloat(value.product_promotion_price) > 0){
    //             $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_promotion_price) * parseFloat(value.exchange_rate)) * parseFloat(value.product_qty)));
    //         }else{
    //             $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_normal_price) * parseFloat(value.exchange_rate)) * parseFloat(value.product_qty)));
    //         }
    //     });
    // };

    // $scope.calcSum();

});
