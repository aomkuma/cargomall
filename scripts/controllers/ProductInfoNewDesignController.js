angular.module('app').controller('ProductInfoNewDesignController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
  //console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    $scope.clearTimeout();
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();
    // console.log($scope.exchange_rate);
    $scope.ProductDetail = sessionStorage.getItem('product_info');
    // $scope.ProductListStorages = angular.fromJson($localStorage.product_list_storage);
    console.log($scope.ProductDetail);
    $scope.ListProductTable = [];
    $scope.checkPriceByColor = function(index){

      // if($scope.ProductDetail.price_list_by_color.length > 0 && $scope.ProductDetail.price_list_by_color[index] > 0){
      //   // $log.log($scope.ProductDetail.price_list_by_color[index]);
      //   $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      // }
      
      $scope.ListProductTable = [];
      $scope.ProductColorList = [];
      var color_list = [];
      var LoopColor = [];
      var LoopType = '';
      if($scope.ProductDetail.product_color_img.length > 0){
        LoopColor = $scope.ProductDetail.product_color_img;
        LoopType = 'image';
      }else{
        LoopColor = $scope.ProductDetail.product_color;
        LoopType = 'text';
      }
      
      for(var i = 0; i < LoopColor.length; i++){

        $scope.ListProductTable = [];
        color_list = [];
        color_list['name'] = null;

        if(LoopType == 'image'){
          color_list['img'] = angular.copy(LoopColor[i]);
        }

        color_list['name'] = angular.copy($scope.ProductDetail.product_color[i]);
        
        console.log($scope.ProductDetail.ProductLevelList.length);
        if($scope.ProductDetail.ProductLevelList.length > 0/* && $scope.ProductDetail.ProductLevelList.length >= $scope.ProductDetail.product_color.length*/){
          $scope.ListProductTable = [];
          var loop = 0;
          var cnt_size = 0;

          while (loop < $scope.ProductDetail.ProductLevelList.length){

            var desc = $scope.ProductDetail.ProductLevelList[loop].description;//.split(" ");
            // console.log('index of : ' + desc[0].indexOf(color_list['name']));
            console.log(color_list['name'] + ' = ' + desc);
            if(/*color_list['name'] == null || */color_list['name'] == desc || $scope.ProductDetail.IsHasItems == false/*color_list['name'].toLowerCase().indexOf(desc.toLowerCase()) >= 0*/){

              // if($scope.ProductDetail.IsHasItems == false){
              //   color_list['name'] = '';
              // }
              var data = angular.copy($scope.ProductDetail);
              $scope.ListProductTable = [];
              $scope.ListProductTable.push(data);
              $scope.ListProductTable[0]['product_color_img'] = LoopColor[i];
              $scope.ListProductTable[0]['product_color_img_choose'] = null;
              $scope.ListProductTable[0]['product_color_choose'] = color_list['name'];

              if($scope.ProductDetail.IsHasItems){

                $scope.ListProductTable[0]['size'] = $scope.ProductDetail.ProductLevelList[loop].description;
                $scope.ListProductTable[0]['product_size_choose'] = $scope.ProductDetail.ProductLevelList[loop].description;
                
              }

              $scope.ListProductTable[0]['product_qty'] = 1;

              if($scope.ProductDetail.ProductLevelList[loop].price){
                console.log('in ProductLevelList[loop]');
                $scope.ListProductTable[0]['product_normal_price'] = parseFloat($scope.ProductDetail.ProductLevelList[loop].price);
              }else if(data.PriceRangeList.length > 0){
                console.log('in PriceRangeList');
                $scope.ListProductTable[0]['product_normal_price'] = parseFloat(data.PriceRangeList[0].price);
              }else{
                console.log('in price_list_by_color');
                $scope.ListProductTable[0]['product_normal_price'] = parseFloat(data.price_list_by_color[i]);

              }  

              if(isNaN($scope.ListProductTable[0]['product_normal_price'])){
                $scope.ListProductTable[0]['product_normal_price'] = $scope.ProductDetail.product_normal_price;
              }

              // console.log($scope.ListProductTable[cnt_size]['product_normal_price']);

              $scope.ListProductTable[0].exchange_rate = parseFloat(data.exchange_rate);
              $scope.ListProductTable[0]['product_price_thb'] = parseFloat(($scope.ListProductTable[0]['product_normal_price'] * data.exchange_rate).toFixed(2));
              // cnt_size++;
              // break;
              loop = $scope.ProductDetail.ProductLevelList.length;

              console.log('if => if');
              // console.log($scope.ProductColorList);
         
            }else{

              var data = angular.copy($scope.ProductDetail);
              $scope.ListProductTable.push(data);
              $scope.ListProductTable[cnt_size]['product_color_img'] = LoopColor[i];
              $scope.ListProductTable[cnt_size]['product_color_img_choose'] = null;
              $scope.ListProductTable[cnt_size]['size'] = $scope.ProductDetail.ProductLevelList[loop].description;
              $scope.ListProductTable[cnt_size]['product_size_choose'] = $scope.ProductDetail.ProductLevelList[loop].description;
              $scope.ListProductTable[cnt_size]['product_qty'] = 1;

              if($scope.ProductDetail.ProductLevelList[loop].price){
                console.log('in ProductLevelList[loop]');
                $scope.ListProductTable[cnt_size]['product_normal_price'] = parseFloat($scope.ProductDetail.ProductLevelList[loop].price);
              }else if(data.PriceRangeList.length > 0){
                console.log('in PriceRangeList');
                $scope.ListProductTable[cnt_size]['product_normal_price'] = parseFloat(data.PriceRangeList[0].price);
              }else{
                console.log('in price_list_by_color');
                $scope.ListProductTable[cnt_size]['product_normal_price'] = parseFloat(data.price_list_by_color[i]);

              }  

              if(isNaN($scope.ListProductTable[cnt_size]['product_normal_price'])){
                $scope.ListProductTable[cnt_size]['product_normal_price'] = $scope.ProductDetail.product_normal_price;
              }

              // console.log($scope.ListProductTable[cnt_size]['product_normal_price']);

              $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
              $scope.ListProductTable[cnt_size]['product_price_thb'] = parseFloat(($scope.ListProductTable[cnt_size]['product_normal_price'] * data.exchange_rate).toFixed(2));
              cnt_size++;

              console.log('if => else');
              

            }

            loop++;

          }     

          color_list['item_list'] = angular.copy($scope.ListProductTable);
          $scope.ProductColorList.push(color_list);

          
          // console.log($scope.ListProductTable);

        }else{
            var data = angular.copy($scope.ProductDetail);
            var cnt_size = 0;
            $scope.ListProductTable.push(data);
            $scope.ListProductTable[cnt_size]['product_color_img'] = LoopColor[i];
            $scope.ListProductTable[cnt_size].product_color_img_display = $scope.ProductDetail.product_color_img[i];
            $scope.ListProductTable[cnt_size].product_color_display = $scope.ProductDetail.product_color[i];
            $scope.ListProductTable[cnt_size].product_color_img_choose = null;

            $scope.ListProductTable[cnt_size].product_size_choose = null;
            $scope.ListProductTable[cnt_size].product_qty = 1;

            if(data.PriceRangeList.length > 0){
              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.PriceRangeList[0].price);
            }else{
              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.price_list_by_color[i]);
            } 

            if(isNaN($scope.ListProductTable[cnt_size]['product_normal_price'])){
              $scope.ListProductTable[cnt_size]['product_normal_price'] = $scope.ProductDetail.product_normal_price;
            }

            if($scope.ListProductTable[cnt_size].product_normal_price != null){
              $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
              $scope.ListProductTable[cnt_size].product_price_thb = parseFloat(($scope.ListProductTable[cnt_size].product_normal_price * data.exchange_rate).toFixed(2));
              cnt_size++;

              color_list['item_list'] = angular.copy($scope.ListProductTable);
              $scope.ProductColorList.push(color_list);
              console.log('else');
              console.log($scope.ProductColorList);
            }
        }

        
      }

      console.log($scope.ProductColorList);


      // if(color_list.length > 0){
      //   $scope.ProductColorList = angular.copy(color_list);  
      // }
      
      
      if( $scope.ProductDetail.ProductLevelList.length > 0 && $scope.ListProductTable.length == 0 && $scope.ProductColorList.length == 0){
          color_list['img'] = null;
          color_list['name'] = null;
          var loop = 0;
          var cnt_size = 0;
          while (loop < $scope.ProductDetail.ProductLevelList.length){
            var data = angular.copy($scope.ProductDetail);
            $scope.ListProductTable.push(data);
            $scope.ListProductTable[0]['product_color_img'] = $scope.ProductDetail.product_image;
            $scope.ListProductTable[cnt_size]['product_color_img_choose'] = null;
            $scope.ListProductTable[cnt_size]['size'] = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size]['product_size_choose'] = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size]['product_qty'] = 1;

            if(data.PriceRangeList.length > 0){
              $scope.ListProductTable[cnt_size]['product_normal_price'] = parseFloat(data.PriceRangeList[0].price);
            }else{

              $scope.ListProductTable[cnt_size]['product_normal_price'] = parseFloat(data.price_list_by_color[i]);

            }  

            if(isNaN($scope.ListProductTable[cnt_size]['product_normal_price'])){
              $scope.ListProductTable[cnt_size]['product_normal_price'] = $scope.ProductDetail.product_normal_price;
            }

            $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
            $scope.ListProductTable[cnt_size]['product_price_thb'] = parseFloat(($scope.ListProductTable[cnt_size]['product_normal_price'] * data.exchange_rate).toFixed(2));
            cnt_size++;
            loop++;

          }

          color_list['item_list'] = angular.copy($scope.ListProductTable);
          $scope.ProductColorList.push(color_list);

          console.log('if 2');
          console.log($scope.ProductColorList);
      }

      if($scope.ProductColorList.length == 0){

        var data = angular.copy($scope.ProductDetail);
        $scope.ListProductTable.push(data);
        $scope.ListProductTable[0]['product_color_img'] = $scope.ProductDetail.product_image;
        $scope.ListProductTable[0]['product_color_img_choose'] = null;
        $scope.ListProductTable[0]['size'] =null
        $scope.ListProductTable[0]['product_size_choose'] = null;
        $scope.ListProductTable[0]['product_qty'] = 1;

        $scope.ListProductTable[0]['product_normal_price'] = $scope.ProductDetail.product_normal_price;

        $scope.ListProductTable[0].exchange_rate = parseFloat(data.exchange_rate);
        $scope.ListProductTable[0]['product_price_thb'] = parseFloat(($scope.ListProductTable[0]['product_normal_price'] * data.exchange_rate).toFixed(2));
        color_list['item_list'] = angular.copy($scope.ListProductTable);
        $scope.ProductColorList.push(color_list);

        console.log('if 3');
        console.log($scope.ProductColorList);
      }

      console.log($scope.ProductColorList);
    }

    $scope.changePrice = function(index, pic_color_url){
      // console.log(pic_color_url);
      if($scope.ProductDetail.price_list_by_color !== undefined && parseFloat($scope.ProductDetail.price_list_by_color[index]) > 0){
        $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      }
      if($scope.ProductDetail.price_list_by_color !== undefined && pic_color_url.startsWith('http')){
        $scope.ProductDetail.product_image = pic_color_url;
        console.log('result ',$scope.ProductDetail.product_image);
      }else{
        $scope.ProductDetail.product_image = $scope.ProductImage; 
      }
      // $scope.ProductDetail.product_image = pic_color_url;
   };

   $scope.setSelectedPrice = function(price, description){
    price = parseFloat(price);
    if(price > 0){
      $scope.ProductDetail.product_normal_price = (price);
    }
    $scope.ProductDetail.remark = description;
   }

   $scope.selectedProduct = function(selected_product){
     if(selected_product == false){
       selected_product = true;
     }else{
       selected_product = false;
     }
     console.log(selected_product);
   }

   $scope.checkPriceLength = function(item, qty){
      if($scope.ProductDetail.PriceRangeList.length > 0){
        for(var i = 0; i < $scope.ProductDetail.PriceRangeList.length; i++){
          
          if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && qty < $scope.ProductDetail.PriceRangeList[i].max_qty){
            item.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }

          }else if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && $scope.ProductDetail.PriceRangeList[i].max_qty == -1){
            item.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }
          }

        }

      }

      if(checkEmptyField(item.product_promotion_price)){
        item.product_price_thb = parseFloat(((item.product_promotion_price * qty) * item.exchange_rate).toFixed(2));
      }else{
        item.product_price_thb = parseFloat(((item.product_normal_price * qty) * item.exchange_rate).toFixed(2));
      }

      console.log(item.product_price_thb);
   }

    $scope.addCart = function(ProductColorList){
      
      if(!checkEmptyField($scope.session_storage.user_data)){
        alert('กรุณาลงชื่อเข้าใช้งานระบบก่อนทำการสั่งซื้อสินค้า');
        return false;
      }

      if(!checkEmptyField($scope.product_list_storage)){
        $scope.ProductDetailList = [];
      }else{
        $scope.ProductDetailList = $scope.product_list_storage;
      //   $scope.ProductDetailList = angular.fromJson($cookies.get('product_list_storage'));
      }

      if($scope.ProductDetailList.length == 20){
        alert('ขออภัยค่ะ ไม่สามารถเพิ่มสินค้าลงตะกร้่าได้ เนื่องจากมีการจำกัด 20 รายการสินค้าต่อ 1 การสั่งซื้อ');
        return false;
      }
      // $scope.ProductDetail.exchange_rate = $scope.$parent.exchange_rate;

      console.log(ProductColorList);

      for(j = 0; j < ProductColorList.length; j++){

        var ListProductTable = angular.copy(ProductColorList[j].item_list);
        for(var i = 0; i < ListProductTable.length; i++){

          if(ListProductTable[i].product_color_img_choose != null){

            if(ListProductTable[i].product_color_img.startsWith('http')){
              ListProductTable[i].product_image = ListProductTable[i].product_color_img;
              ListProductTable[i].product_color_choose = '';
            }

            if(ListProductTable[i].product_color_img_choose != '' && !ListProductTable[i].product_color_img_choose.startsWith('http')){
              ListProductTable[i].product_color_choose = angular.copy(ListProductTable[i].product_color_img_choose);
              ListProductTable[i].product_color_img_choose = '';
            }
            // else {//if(ListProductTable[i].product_color_img_choose != ''&& ListProductTable[i].product_color_img_choose.startsWith('http')){
              // ListProductTable[i].product_color_choose = '';
            // }

            $scope.ProductDetailList.push(ListProductTable[i]);

            if($scope.ProductDetailList.length == 20){
              alert('ขออภัยค่ะ ไม่สามารถเพิ่มสินค้าลงตะกร้่าได้ เนื่องจากมีการจำกัด 20 รายการสินค้าต่อ 1 การสั่งซื้อ');
              break;
            }
          }

        }

      }

      console.log($scope.ProductDetailList);
      // return false;
      // $log.log($scope.ProductDetailList);
      // $localStorage.product_list_storage = JSON.stringify($scope.ProductDetailList);
      // $cookies.put('product_list_storage', JSON.stringify($scope.ProductDetailList));
      // $log.log($cookies.get('product_list_storage'));
      // window.location.href = 'view-orders';
      IndexOverlayFactory.overlayShow();
      
      var params = {'cart_desc' : JSON.stringify($scope.ProductDetailList)};
      HTTPService.clientRequest('cart/update', params).then(function(result){

        if(result.data.STATUS == 'OK'){
          // window.location.reload();
          window.location.href = 'view-orders';
        }
        
        IndexOverlayFactory.overlayHide();
      });

    }

    if($scope.ProductDetail != null && $scope.ProductDetail != ''){
      $scope.ProductDetail = angular.fromJson($scope.ProductDetail);
      $scope.ProductImage = angular.copy($scope.ProductDetail.product_image);
      // $log.log($scope.ProductDetail);
      if(!checkEmptyField($scope.ProductDetail['price_type'])){
        $scope.ProductDetail['price_type'] = 'normal';
        // $log.log($scope.ProductDetail.ProductLevelList);
      }

      // if($scope.ProductDetail.ProductLevelList && $scope.ProductDetail.ProductLevelList.length > 0){
        // $scope.ProductDetail.product_normal_price = 0;
      // }

      // setTimeout(function(){
        $scope.checkPriceByColor(0);
      // }, 3000);
      
      
    }

    $scope.dialogRemoveItem = function(index){
        
        if(!$scope.$parent.ShowDialogRemoveItem){
            $scope.$parent.ShowDialogRemoveItem = true;
            $scope.$parent.RemoveItemIndex = index;
        }
    }

    $scope.autoUpdateOrder = function(index, product){

        $scope.ProductListStorage[index] = product;
        $localStorage.product_list_storage = JSON.stringify($scope.ProductListStorage);
        
        // console.log(angular.fromJson($localStorage.product_list_storage));
        var params = {'cart_desc' : JSON.stringify($scope.ProductListStorage)};
        HTTPService.clientRequest('cart/update', params).then(function(result){

          if(result.data.STATUS == 'OK'){
            // window.location.reload();
            // window.location.href = 'view-orders';
          }
          
          IndexOverlayFactory.overlayHide();
        });
    }

});
