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
	
	//directive for funding deadlines
	app.directive('fundingDeadlines', function(){
	   return{
		   restrict:'E',
		   templateUrl:"tpl/funding-deadlines.tpl.html"
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
		
		navbarCtrl.navBarFundingItems = [];
		$http.get("ajax/nav-bar-fundings.php").success(function(data){
		   navbarCtrl.navBarFundingItems = data; 
		});

	}]);

	//main controller for the content - will utilize http, scope, log, and interval
	app.controller('FundingController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
		
		var fundingCtrl = this;
		fundingCtrl.currentFilterType = 'recommended';
		
		//get the filter-funding
		fundingCtrl.filter = [];
		$http.get("ajax/filter-funding.php").success(function(data){
			fundingCtrl.filter = data;
		});
		
		fundingCtrl.fundingItems = [];
		
		//get the main funding items with the get method in $http service
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
		
		//check if funding is already favorited
		$scope.favoritedCheck = function(toCheck, arrayToCheck){
			for(var i = 0; i < arrayToCheck.length; i++ ){
				if( arrayToCheck[i] == toCheck){
					return true;
				}
			}
			return false;
		};
		
		//check if funding is already shared
		$scope.sharedCheck = function(toCheck, arrayToCheck){
			for(var i = 0; i < arrayToCheck.length; i++ ){
				if( arrayToCheck[i] == toCheck){
					return true;
				}
			}
			return false;
		};
		
		//currentFilterType for the funding controller
		$scope.fundingItemCall(fundingCtrl.currentFilterType);
		
		//getting funding deadlines
		fundingCtrl.fundingDeadlines = [];
		$http.get("ajax/funding-deadlines.php").success(function(data){
		   fundingCtrl.fundingDeadlines = data; 
		});
		
		//views the already favorited fundings
		fundingCtrl.favoritedFunding = [];
		$http.get("scripts/view_favorite_fundings.php").success(function(data){
		   fundingCtrl.favoritedFunding = data; 
		});
		
		//views the already shared fundings
		fundingCtrl.sharedFunding = [];
		$http.get("scripts/view_share_fundings.php").success(function(data){
		   fundingCtrl.sharedFunding = data; 
		});
		
		//call to refresh funding filter, funding items, and event deadlines
		$scope.refreshCall = function(){
//          wondering if this causing the highlight to go away
//			$http.get("ajax/filter-funding.php").success(function(data){
//				fundingCtrl.filter = data;
//			});
			
			$http.get("ajax/funding-items.php").success(function(data){
				fundingCtrl.fundingItems = data;
			});
			
			$http.get("ajax/funding-deadlines.php").success(function(data){
			   fundingCtrl.fundingDeadlines = data; 
			});
		};
		
		//add favorite opportunity
		$scope.addFavorite = function(fundID){
			$http({
					method:'GET',
					url:'scripts/favorite_funding.php',
					params:{
						id: fundID
					}
				}).success(function(data){
					$scope.refreshCall();
					$http.get("scripts/view_favorite_fundings.php").success(function(data){
					   fundingCtrl.favoritedFunding = data; 
					});
				});
		}
		
		//setting refreshCall at an interval
		$interval(function(){$scope.refreshCall();}, 10000);
		
		//add funding deadline 
		$scope.addFundingDeadline = function(fundID){
			$http({
				method:"GET",
				url:"scripts/insert_funding_deadlines.php",
				params:{ id: fundID }
			}).success(function(data){
				$scope.refreshCall();
			});
		};
		
		//removing funding deadline
		$scope.removeFundingDeadline = function(fundID){
			$http({
				method:"GET",
				url:"scripts/delete_funding_deadlines.php",
				params:{ id: fundID }
			}).success(function(data){
				$scope.refreshCall();
			});
		};
	}]);
	
	//Controller for managing the share funding button
	app.controller('PeopleController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
		
		var peopleCtrl = this;
		peopleCtrl.fundingShareUsers = [];
//        peopleCtrl.selectedUsers = [];
		
		//get users with a defined quantity of names to display
		$http.get("scripts/get_users_funding.php").success(function(data){
			peopleCtrl.fundingShareUsers = data; 
		});
		$scope.quantity = 5;
		
		/*This function shares the funding opportunity based on the funding opportunity id.
			It is separated from the funding controller in order to have each funding opportunity
			display the other users' emails independently (this prevents the checkbox from being 
			true on all funding opportunities).*/
		$scope.shareWithUsers = function(){
			var userString = '';
			
			angular.forEach(peopleCtrl.fundingShareUsers, function(fundingShareUsers) {
				if(!!fundingShareUsers.selected) userString = userString + fundingShareUsers.email + ',';
			})
			
			console.log(userString);
			console.log($scope.fundItem.id);
			
			if(userString.length != 0){
				userString = userString.substring(0, userString.length - 1);
				$http({
					method:'GET',
					url:'scripts/share.php',
					params:{
						user: userString,
						id: $scope.fundItem.id,
						type: 'funding'
					}
				}).success(function(data){
					$scope.refreshCall();
				});
			}
		};
		
	}]);
	
	app.controller('FavoriteController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
		console.log("FUNDID: "+$scope.fundItem.id);
//		$scope.addFavorite = function(){
//			$http({
//					method:'GET',
//					url:'scripts/favorite_funding.php',
//					params:{
//						id: $scope.fundItem.id
//					}
//				}).success(function(data){
//					$scope.refreshCall();
//				});
//		}
		
	}]);
})();
