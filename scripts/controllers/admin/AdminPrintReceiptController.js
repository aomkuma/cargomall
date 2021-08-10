angular.module('app').controller('AdminPrintReceiptController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, $routeParams, HTTPService, IndexOverlayFactory) {
	    $scope.clearTimeout();
	// if(!checkEmptyField($scope.session_storage.user_data)){
 //      // alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
 //      window.location.replace('admin/signin');
 //      // history.back();
 //      return;
 //    }

 //    if(!checkEmptyField($scope.UserData.is_admin) && !$scope.UserData.is_admin){
 //      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
 //      history.back();
 //      return;
 //    }

    $templateCache.removeAll();

    $scope.getData = function(pay_id){
      IndexOverlayFactory.overlayShow();
        var params = {'pay_id' : pay_id};
        HTTPService.clientRequest('admin/receipt/get-data', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.ReceiptData = result.data.DATA.ReceiptData;
          $scope.RunningData = result.data.DATA.RunningData;
          $scope.ItemList = result.data.DATA.ItemList;
          $scope.SumPayAmount = result.data.DATA.SumPayAmount;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.export = function(){
      
        html2canvas(document.querySelector("#exportthis")).then(canvas => {
          // document.body.appendChild(canvas)
          var data = canvas.toDataURL();

          var docDefinition = {
              content: [{
                  image: data,
                  width: 500
              }]
          };

          var name = '';
          if($scope.ReceiptData.pay_type == 2){
            name = 'Order_Trans_';
          }else{
            name = 'Import_';
          }

          pdfMake.createPdf(docDefinition).download("CARGOMALL_Receipt_" + name + $scope .RunningData.running_no + ".pdf");
        });
     }

    $scope.pay_id = $routeParams.pay_id;

    $scope.getData($scope.pay_id);

});
