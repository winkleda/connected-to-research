(function(){

	var app = angular.module('funding', ['ui.bootstrap']);
	
    //directive for Navbar under here
    app.directive('userNavBar', function(){
		return{
			restrict: 'E',
			templateUrl:'tpl/nav-bar-funding.tpl.html'
		};
	});
    
	//directive for filtering funding items
	app.directive('filterFunding', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/filter-funding.tpl.html"
		};
	});
	
	//directive for funding items
	app.directive('fundingItem', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/funding-item.tpl.html"
		};
	});
    
	//directive for research events and deadlines under here
    app.directive('researchEventsAndDeadlines', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/research-events-deadlines.tpl.html'
		};
	});
    
    //controller for NavBar - to display user info up top, 
    //uses same NavbarController of previous publications team
    app.controller('NavbarController', ['$http', function($http){
		var navbarCtrl = this;

		navbarCtrl.userInfo = [];
		$http.get("ajax/user-nav-bar-info.php").success(function(data){
			navbarCtrl.userInfo = data;
		});

	}]);

	//main controller for the content - will utilize http, scope, log, and interval
	app.controller('FundingController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
		
		var fundingCtrl = this;
        fundingCtrl.currentFilterType = 'sourceFBO';

        //get the filter-funding
		fundingCtrl.filter = [];
		$http.get("ajax/filter-funding.php").success(function(data){
			fundingCtrl.filter = data;
		});

		fundingCtrl.fundingItems = [];
		
        //get the main funding items
		$scope.fundingItemCall = function(type){
			fundingCtrl.currentFilterType = type;

			$http({
				method:'GET',
				url:'ajax/funding-items.php',
				params:{
					type:fundingCtrl.currentFilterType
				}
			}).success(function(data){
				fundingCtrl.fundingItems = data;
			});
		};

        //currentFilterType for the funding controller
		$scope.fundingItemCall(fundingCtrl.currentFilterType);
		
		fundingCtrl.researchEventsDeadlines = [];
		$http.get("ajax/research-events-deadlines.php").success(function(data){
			fundingCtrl.researchEventsDeadlines = data;
		});

        //call to refresh funding filter and event deadlines
		$scope.refreshCall = function(){
			$http.get("ajax/filter-funding.php").success(function(data){
				fundingCtrl.filter = data;
			});
		
			$http.get("ajax/research-events-deadlines.php").success(function(data){
				fundingCtrl.researchEventsDeadlines = data;
			});
		};
		
//        $scope.favoriteArticle = function(){
//			
//		};
        
        //setting refreshCall at an interval
		$interval(function(){$scope.refreshCall();}, 10000);
		
	}]);
    
    app.controller('PeopleController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
        
        var peopleCtrl = this;
        
        peopleCtrl.fundingShareUsers = [];
        if($scope !== ''){
            $http.get("ajax/shareFunding.php").success(function(data){
            peopleCtrl.fundingShareUsers = data; 
            });
        }
    }]);
	
})();
